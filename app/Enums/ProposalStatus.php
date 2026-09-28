<?php

namespace App\Enums;

enum ProposalStatus: string
{
    case Draft = 'draft';
    case SupervisorValidation1 = 'supervisor_validation_1';
    case AdminAssignment = 'admin_assignment';
    case AdminReview = 'admin_review';
    case SubstantiveAssignment = 'substantive_assignment';
    case SubstantiveReview = 'substantive_review';
    case Revision = 'revision';
    case SupervisorValidation2 = 'supervisor_validation_2';
    case FinalReviewAssignment = 'final_review_assignment';
    case FinalReview = 'final_review';
    case SemifinalDecision = 'semifinal_decision';
    case UniversityAssignment = 'university_assignment';
    case FinalUpload = 'final_upload';
    case UniversityValidation = 'university_validation';
    case FinalDecision = 'final_decision';
    case NotPassed = 'not_passed';
    case InternalPassed = 'internal_passed';
    case InternalNotPassed = 'internal_not_passed';
    case Withdrawn = 'withdrawn';
    case Disqualified = 'disqualified';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::SupervisorValidation1 => 'Validasi Pembimbing (1)',
            self::AdminAssignment => 'Penugasan Reviewer Administratif',
            self::AdminReview => 'Review Administratif',
            self::SubstantiveAssignment => 'Penugasan Reviewer Substantif',
            self::SubstantiveReview => 'Review Substantif Awal',
            self::Revision => 'Revisi',
            self::SupervisorValidation2 => 'Validasi Pembimbing (2)',
            self::FinalReviewAssignment => 'Penugasan Reviewer Final',
            self::FinalReview => 'Review Final',
            self::SemifinalDecision => 'Keputusan Semifinal',
            self::UniversityAssignment => 'Penugasan Dosen Universitas',
            self::FinalUpload => 'Unggah Revisi Akhir',
            self::UniversityValidation => 'Validasi Dosen Universitas',
            self::FinalDecision => 'Keputusan Akhir',
            self::NotPassed => 'Tidak Lolos',
            self::InternalPassed => 'Lolos Evaluasi Internal',
            self::InternalNotPassed => 'Tidak Lolos Evaluasi Internal',
            self::Withdrawn => 'Ditarik',
            self::Disqualified => 'Didiskualifikasi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Revision, self::FinalUpload => 'yellow',
            self::SupervisorValidation1, self::SupervisorValidation2,
            self::AdminReview, self::SubstantiveReview,
            self::FinalReview, self::SemifinalDecision,
            self::UniversityValidation, self::FinalDecision => 'blue',
            self::AdminAssignment, self::SubstantiveAssignment,
            self::FinalReviewAssignment, self::UniversityAssignment => 'indigo',
            self::InternalPassed => 'green',
            self::NotPassed, self::InternalNotPassed,
            self::Withdrawn, self::Disqualified => 'red',
        };
    }

    /** Status akhir — tidak bisa berubah lagi. */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::NotPassed,
            self::InternalPassed,
            self::InternalNotPassed,
            self::Withdrawn,
            self::Disqualified,
        ]);
    }

    /** Giliran siapa sekarang. */
    public function holder(): string
    {
        return match ($this) {
            self::Draft, self::Revision, self::FinalUpload => 'Mahasiswa',
            self::SupervisorValidation1, self::SupervisorValidation2 => 'Dosen Pembimbing',
            self::AdminReview, self::SubstantiveReview, self::FinalReview => 'Reviewer',
            self::UniversityValidation => 'Dosen Universitas',
            self::AdminAssignment, self::SubstantiveAssignment,
            self::FinalReviewAssignment, self::UniversityAssignment,
            self::SemifinalDecision, self::FinalDecision => 'Operator / Pimpinan PT',
            default => '—',
        };
    }
}
