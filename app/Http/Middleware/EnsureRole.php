<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Pastikan user memiliki salah satu dari peran yang diizinkan.
     * Contoh penggunaan: middleware('role:operator,super_operator')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $allowedRoles = array_map(fn (string $r) => Role::from($r), $roles);

        if (! $request->user()->hasAnyRole(...$allowedRoles)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki peran yang diperlukan untuk halaman ini.');
        }

        return $next($request);
    }
}
