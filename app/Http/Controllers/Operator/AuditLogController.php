<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\StatusHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = StatusHistory::with(['proposal', 'actor'])
            ->latest()
            ->paginate(50);
            
        // Ambil info failed jobs jika perlu (opsional, tabel dari laravel)
        $failedJobs = DB::table('failed_jobs')->count();
        $pendingJobs = DB::table('jobs')->count();

        return view('operator.audit_logs.index', compact('logs', 'failedJobs', 'pendingJobs'));
    }
}
