<?php

namespace App\Http\Controllers\SuperOperator;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperOperator\StoreUserRequest;
use App\Http\Requests\SuperOperator\UpdateUserRequest;
use App\Models\User;
use App\Services\AccountClaimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly AccountClaimService $claimService) {}

    public function index(): View
    {
        $users = User::with('roleRecords')
            ->orderBy('name')
            ->paginate(25);

        return view('super-operator.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::cases();

        return view('super-operator.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'nim' => $validated['nim'] ?? null,
                'nidn' => $validated['nidn'] ?? null,
                'nuptk' => $validated['nuptk'] ?? null,
                'study_program' => $validated['study_program'] ?? null,
                'cohort_year' => $validated['cohort_year'] ?? null,
                'password' => Hash::make(Str::random(32)), // Tidak bisa dipakai
                'must_change_password' => true,
                'is_active' => true,
            ]);

            foreach ($validated['roles'] as $roleValue) {
                $user->roleRecords()->create(['role' => $roleValue]);
            }

            return $user;
        });

        // Kirim token klaim via email (log saat dev)
        $token = $this->claimService->generateClaimToken($user);

        // TODO: dispatch(new SendClaimEmail($user, $token)); — Fase berikutnya
        // Sementara log token agar bisa ditest
        logger()->info("Claim token untuk {$user->email}: {$token}");

        return redirect()->route('super-operator.users.index')
            ->with('success', "Akun {$user->name} berhasil dibuat. Email klaim telah dikirim ke {$user->email}.");
    }

    public function show(User $user): View
    {
        $user->load('roleRecords');

        return view('super-operator.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $user->load('roleRecords');
        $roles = Role::cases();

        return view('super-operator.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $user): void {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'nim' => $validated['nim'] ?? null,
                'nidn' => $validated['nidn'] ?? null,
                'nuptk' => $validated['nuptk'] ?? null,
                'study_program' => $validated['study_program'] ?? null,
                'cohort_year' => $validated['cohort_year'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Sync roles
            $user->roleRecords()->delete();
            foreach ($validated['roles'] as $roleValue) {
                $user->roleRecords()->create(['role' => $roleValue]);
            }
        });

        return redirect()->route('super-operator.users.index')
            ->with('success', "Akun {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        // Jangan hapus akun yang sedang login
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
        }

        $user->delete();

        return redirect()->route('super-operator.users.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    /** Kirim ulang email klaim */
    public function resendClaim(User $user): RedirectResponse
    {
        $token = $this->claimService->generateClaimToken($user);
        logger()->info("Resend claim token untuk {$user->email}: {$token}");

        return back()->with('success', "Email klaim telah dikirim ulang ke {$user->email}.");
    }

    /** Toggle status aktif/nonaktif */
    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }
}
