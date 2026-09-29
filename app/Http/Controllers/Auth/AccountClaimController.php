<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccountClaimService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AccountClaimController extends Controller
{
    public function __construct(private readonly AccountClaimService $claimService) {}

    public function show(User $user, string $token)
    {
        return view('auth.claim', compact('user', 'token'));
    }

    public function claim(Request $request, User $user, string $token)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $success = $this->claimService->claimAccount($user, $token, $request->input('password'));

        if (! $success) {
            return back()->with('error', 'Token klaim tidak valid atau sudah kadaluarsa.');
        }

        return redirect()->route('login')->with('status', 'Akun berhasil diklaim. Silakan login dengan kata sandi baru Anda.');
    }
}
