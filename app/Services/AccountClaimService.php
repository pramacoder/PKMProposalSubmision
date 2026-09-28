<?php

namespace App\Services;

use App\Models\AccountClaim;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountClaimService
{
    /**
     * Membuat token klaim untuk user yang baru di-invite/didaftarkan oleh ketua,
     * dan menyimpannya di tabel account_claims.
     * Mengembalikan raw token yang dikirim via email.
     */
    public function generateClaimToken(User $user): string
    {
        // Invalidasi token lama jika ada
        AccountClaim::where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]); // Tandai used agar expired

        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        AccountClaim::create([
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addDays(7),
        ]);

        return $rawToken;
    }

    /**
     * Memvalidasi token dan memproses klaim akun.
     */
    public function claimAccount(User $user, string $rawToken, string $newPassword): bool
    {
        $tokenHash = hash('sha256', $rawToken);

        $claim = AccountClaim::where('user_id', $user->id)
            ->where('token_hash', $tokenHash)
            ->first();

        if (! $claim || ! $claim->isValid()) {
            return false;
        }

        // Update password & status
        $user->update([
            'password' => Hash::make($newPassword),
            'claimed_at' => now(),
            'must_change_password' => false,
            'is_active' => true,
        ]);

        // Tandai token terpakai
        $claim->update([
            'used_at' => now(),
            'requested_ip' => request()->ip(),
        ]);

        return true;
    }
}
