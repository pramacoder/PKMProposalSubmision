<?php

namespace App\Services;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Models\StatusHistory;
use App\Models\User;
use App\Mail\ProposalStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

/**
 * Satu-satunya pintu untuk mengubah status proposal (RULE-12).
 * Semua transisi status harus melalui service ini.
 */
class ProposalWorkflowService
{
    /**
     * Transisi status yang diizinkan: [dari => [ke, ...]]
     *
     * @var array<string, string[]>
     */
    private const TRANSITIONS = [
        'draft' => ['supervisor_validation_1', 'withdrawn'],
        'supervisor_validation_1' => ['admin_assignment', 'draft'],
        'admin_assignment' => ['admin_review'],
        'admin_review' => ['substantive_assignment'],
        'substantive_assignment' => ['substantive_review'],
        'substantive_review' => ['revision'],
        'revision' => ['supervisor_validation_2', 'withdrawn'],
        'supervisor_validation_2' => ['final_review_assignment', 'revision'],
        'final_review_assignment' => ['final_review'],
        'final_review' => ['semifinal_decision'],
        'semifinal_decision' => ['university_assignment', 'not_passed'],
        'university_assignment' => ['final_upload'],
        'final_upload' => ['university_validation'],
        'university_validation' => ['final_decision', 'final_upload'],
        'final_decision' => ['internal_passed', 'internal_not_passed'],
        // Kapan saja operator/pimpinan bisa mendiskualifikasi
        'admin_review' => ['substantive_assignment', 'disqualified'],
    ];

    public function transition(
        Proposal $proposal,
        ProposalStatus $toStatus,
        User $actor,
        ?string $note = null
    ): void {
        DB::transaction(function () use ($proposal, $toStatus, $actor, $note): void {
            $fromStatus = $proposal->status;

            if (! $this->canTransition($fromStatus, $toStatus)) {
                throw new InvalidArgumentException(
                    "Transisi dari '{$fromStatus->value}' ke '{$toStatus->value}' tidak diizinkan."
                );
            }

            // Catat riwayat
            StatusHistory::create([
                'proposal_id' => $proposal->id,
                'from_status' => $fromStatus->value,
                'to_status' => $toStatus->value,
                'actor_id' => $actor->id,
                'note' => $note,
            ]);

            // Update status
            $extra = [];
            if ($toStatus === ProposalStatus::SupervisorValidation1) {
                $extra['submitted_at'] = now();
            }

            $proposal->update(array_merge(['status' => $toStatus], $extra));

            // Kirim notifikasi email via Queue
            $this->dispatchEmailNotifications($proposal, $toStatus, $note ?? '');
        });
    }

    private function dispatchEmailNotifications(Proposal $proposal, ProposalStatus $status, string $note): void
    {
        $proposal->loadMissing(['leader', 'supervisor']);

        $recipients = [];

        // Tentukan penerima berdasarkan status
        if (in_array($status, [ProposalStatus::SupervisorValidation1, ProposalStatus::SupervisorValidation2])) {
            if ($proposal->supervisor) {
                $recipients[] = $proposal->supervisor;
            }
        } elseif (in_array($status, [ProposalStatus::Revision, ProposalStatus::FinalUpload, ProposalStatus::InternalPassed, ProposalStatus::InternalNotPassed, ProposalStatus::NotPassed, ProposalStatus::Withdrawn])) {
            $recipients[] = $proposal->leader;
            
            // Untuk keputusan penting, info juga dikirim ke pembimbing
            if (in_array($status, [ProposalStatus::InternalPassed, ProposalStatus::InternalNotPassed]) && $proposal->supervisor) {
                $recipients[] = $proposal->supervisor;
            }
        }

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new ProposalStatusUpdated($proposal, $note));
        }
    }

    public function canTransition(ProposalStatus $from, ProposalStatus $to): bool
    {
        $allowed = self::TRANSITIONS[$from->value] ?? [];

        return in_array($to->value, $allowed, true);
    }

    /** Submit draf/revisi → supervisor_validation_1 / 2 */
    public function submit(Proposal $proposal, User $actor): void
    {
        $targetStatus = ProposalStatus::SupervisorValidation1;
        if ($proposal->status === ProposalStatus::Revision) {
            $targetStatus = ProposalStatus::SupervisorValidation2;
        } elseif ($proposal->status === ProposalStatus::FinalUpload) {
            $targetStatus = ProposalStatus::UniversityValidation;
        }

        if ($targetStatus === ProposalStatus::SupervisorValidation2 && $proposal->currentRevision) {
            $proposal->currentRevision->update(['submitted_at' => now()]);
        }

        $note = 'Proposal diajukan kembali';
        if ($targetStatus === ProposalStatus::SupervisorValidation2) {
            $note .= ' (Revisi)';
        } elseif ($targetStatus === ProposalStatus::UniversityValidation) {
            $note .= ' (Revisi Akhir)';
        }

        $this->transition($proposal, $targetStatus, $actor, $note);
    }

    /** Pembimbing setuju → admin_assignment */
    public function supervisorApprove(Proposal $proposal, User $actor, ?string $note = null): void
    {
        $this->transition($proposal, ProposalStatus::AdminAssignment, $actor, $note ?? 'Disetujui pembimbing');
    }

    /** Pembimbing tolak → draft */
    public function supervisorReject(Proposal $proposal, User $actor, string $reason): void
    {
        $this->transition($proposal, ProposalStatus::Draft, $actor, $reason);
    }

    /** Tarik proposal */
    public function withdraw(Proposal $proposal, User $actor, string $reason): void
    {
        $this->transition($proposal, ProposalStatus::Withdrawn, $actor, $reason);
    }
}
