<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreProposalMemberRequest;
use App\Models\Proposal;
use App\Models\ProposalMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProposalMemberController extends Controller
{
    public function index(Proposal $proposal): View
    {
        $this->authorizeAccess($proposal);

        $proposal->load('members.user');

        return view('student.proposals.members.index', compact('proposal'));
    }

    public function store(StoreProposalMemberRequest $request, Proposal $proposal): RedirectResponse
    {
        $this->authorizeAccess($proposal);

        if (! $proposal->isEditable()) {
            return back()->with('error', 'Proposal sudah terkunci.');
        }

        $validated = $request->validated();

        // Pastikan tidak duplikat
        if ($proposal->members()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with('error', 'Mahasiswa tersebut sudah menjadi anggota tim ini.');
        }

        $proposal->members()->create([
            'cycle_id' => $proposal->cycle_id,
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function destroy(Proposal $proposal, ProposalMember $member): RedirectResponse
    {
        $this->authorizeAccess($proposal);

        if (! $proposal->isEditable()) {
            return back()->with('error', 'Proposal sudah terkunci.');
        }

        if ($member->proposal_id !== $proposal->id) {
            abort(404);
        }

        $member->delete();

        return back()->with('success', 'Anggota berhasil dihapus.');
    }

    private function authorizeAccess(Proposal $proposal): void
    {
        if ($proposal->leader_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }
    }
}
