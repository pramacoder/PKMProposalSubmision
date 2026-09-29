<?php

namespace App\Http\Middleware;

use App\Models\Cycle;
use App\Models\PhaseWindow;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PhaseGate
{
    /**
     * Blokir aksi tulis jika jendela fase sedang ditutup.
     * Contoh penggunaan: middleware('phase:student_submission')
     * Hanya memblokir metode non-GET (POST, PUT, PATCH, DELETE).
     */
    public function handle(Request $request, Closure $next, string $phase): Response
    {
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        $activeCycle = Cycle::active()->first();

        if (! $activeCycle) {
            abort(403, 'Tidak ada siklus aktif saat ini.');
        }

        $window = PhaseWindow::where('cycle_id', $activeCycle->id)
            ->where('phase', $phase)
            ->first();

        if ($window && ! $window->isOpen()) {
            abort(403, 'Jendela fase "'.$phase.'" sedang ditutup. Silakan hubungi operator.');
        }

        return $next($request);
    }
}
