<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'PKM Udayana') }}</title>

    <!-- Meta SEO -->
    <meta name="description" content="Sistem Pengajuan Proposal PKM Universitas Udayana">

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-gray-50" x-data="{ sidebarOpen: false }">

    {{-- ════════════════════ SIDEBAR ════════════════════ --}}
    <aside class="sidebar" :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
           x-cloak
           @keydown.escape.window="sidebarOpen = false">

        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: hsl(210 85% 45%)">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="sidebar-logo-text">
                <span class="text-white/90">PKM</span>
                <span style="color: hsl(38 95% 52%)"> Udayana</span>
                <div class="text-xs font-normal text-white/40 mt-0.5">Sistem Pengajuan Proposal</div>
            </div>
        </div>

        {{-- Navigation per-peran --}}
        <nav class="sidebar-nav">
            @php $user = auth()->user(); @endphp

            {{-- ── MAHASISWA ── --}}
            @if($user->hasRole(\App\Enums\Role::Student))
                <div class="sidebar-section-title">Mahasiswa</div>
                <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.*') ? 'active' : '' }}">
                    <x-icon name="home" class="icon"/>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('student.proposals.index') }}" class="sidebar-link {{ request()->routeIs('student.proposals*') ? 'active' : '' }}">
                    <x-icon name="document-text" class="icon"/>
                    <span>Proposal Saya</span>
                </a>
            @endif

            {{-- ── DOSEN PEMBIMBING ── --}}
            @if($user->hasRole(\App\Enums\Role::Supervisor))
                <div class="sidebar-section-title">Dosen Pembimbing</div>
                <a href="{{ route('supervisor.dashboard') }}" class="sidebar-link {{ request()->routeIs('supervisor.*') ? 'active' : '' }}">
                    <x-icon name="home" class="icon"/>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('supervisor.proposals.index') }}" class="sidebar-link {{ request()->routeIs('supervisor.proposals*') ? 'active' : '' }}">
                    <x-icon name="clipboard-list" class="icon"/>
                    <span>Bimbingan Saya</span>
                </a>
            @endif

            {{-- ── REVIEWER ── --}}
            @if($user->hasRole(\App\Enums\Role::Reviewer))
                <div class="sidebar-section-title">Reviewer</div>
                <a href="{{ route('reviewer.dashboard') }}" class="sidebar-link {{ request()->routeIs('reviewer.*') ? 'active' : '' }}">
                    <x-icon name="home" class="icon"/>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('reviewer.assignments.index') }}" class="sidebar-link {{ request()->routeIs('reviewer.assignments*') ? 'active' : '' }}">
                    <x-icon name="star" class="icon"/>
                    <span>Penugasan Review</span>
                </a>
            @endif

            {{-- ── DOSEN UNIVERSITAS ── --}}
            @if($user->hasRole(\App\Enums\Role::UniversityLecturer))
                <div class="sidebar-section-title">Dosen Universitas</div>
                <a href="{{ route('university.dashboard') }}" class="sidebar-link {{ request()->routeIs('university.*') ? 'active' : '' }}">
                    <x-icon name="home" class="icon"/>
                    <span>Dashboard</span>
                </a>
            @endif

            {{-- ── OPERATOR ── --}}
            @if($user->hasAnyRole(\App\Enums\Role::Operator, \App\Enums\Role::SuperOperator))
                <div class="sidebar-section-title">Operator</div>
                <a href="{{ route('operator.dashboard') }}" class="sidebar-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                    <x-icon name="home" class="icon"/>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('operator.assignments.index') }}" class="sidebar-link {{ request()->routeIs('operator.assignments*') ? 'active' : '' }}">
                    <x-icon name="users" class="icon"/>
                    <span>Penugasan Reviewer</span>
                </a>
                <a href="{{ route('operator.recap.index') }}" class="sidebar-link {{ request()->routeIs('operator.recap*') ? 'active' : '' }}">
                    <x-icon name="document-text" class="icon"/>
                    <span>Rekapitulasi Review</span>
                </a>
                <a href="{{ route('operator.final-assignments.index') }}" class="sidebar-link {{ request()->routeIs('operator.final-assignments*') ? 'active' : '' }}">
                    <x-icon name="star" class="icon"/>
                    <span>Penugasan Final</span>
                </a>
                <a href="{{ route('operator.semifinal.index') }}" class="sidebar-link {{ request()->routeIs('operator.semifinal*') ? 'active' : '' }}">
                    <x-icon name="trophy" class="icon"/>
                    <span>Keputusan Semifinal</span>
                </a>
                <a href="{{ route('operator.university-assignments.index') }}" class="sidebar-link {{ request()->routeIs('operator.university-assignments*') ? 'active' : '' }}">
                    <x-icon name="academic-cap" class="icon"/>
                    <span>Penugasan Univ</span>
                </a>
                <a href="{{ route('operator.decision-batches.index') }}" class="sidebar-link {{ request()->routeIs('operator.decision-batches*') ? 'active' : '' }}">
                    <x-icon name="clipboard-document-check" class="icon"/>
                    <span>Keputusan Akhir</span>
                </a>
                <a href="{{ route('operator.pimnas.index') }}" class="sidebar-link {{ request()->routeIs('operator.pimnas*') ? 'active' : '' }}">
                    <x-icon name="star" class="icon"/>
                    <span>Status PIMNAS</span>
                </a>
                <a href="{{ route('operator.control.index') }}" class="sidebar-link {{ request()->routeIs('operator.control*') ? 'active' : '' }}">
                    <x-icon name="adjustments-horizontal" class="icon"/>
                    <span>Ruang Kontrol</span>
                </a>
                <a href="{{ route('operator.audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('operator.audit-logs*') ? 'active' : '' }}">
                    <x-icon name="clock" class="icon"/>
                    <span>Log Sistem</span>
                </a>
                <a href="{{ route('operator.checklist.index') }}" class="sidebar-link {{ request()->routeIs('operator.checklist*') ? 'active' : '' }}">
                    <x-icon name="clipboard-document-check" class="icon"/>
                    <span>Form Administratif</span>
                </a>
                <a href="{{ route('operator.rubric.index') }}" class="sidebar-link {{ request()->routeIs('operator.rubric*') ? 'active' : '' }}">
                    <x-icon name="chart-bar" class="icon"/>
                    <span>Rubrik Penilaian</span>
                </a>
            @endif

            {{-- ── SUPER OPERATOR ── --}}
            @if($user->hasRole(\App\Enums\Role::SuperOperator))
                <div class="sidebar-section-title">Pimpinan PT</div>
                <a href="{{ route('super-operator.dashboard') }}" class="sidebar-link {{ request()->routeIs('super-operator.dashboard') ? 'active' : '' }}">
                    <x-icon name="home" class="icon"/>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('super-operator.users.index') }}" class="sidebar-link {{ request()->routeIs('super-operator.users*') ? 'active' : '' }}">
                    <x-icon name="users" class="icon"/>
                    <span>Kelola Akun</span>
                </a>
                <a href="{{ route('super-operator.cycles.index') }}" class="sidebar-link {{ request()->routeIs('super-operator.cycles*') ? 'active' : '' }}">
                    <x-icon name="calendar" class="icon"/>
                    <span>Siklus PKM</span>
                </a>
                <a href="{{ route('super-operator.reports.index') }}" class="sidebar-link {{ request()->routeIs('super-operator.reports*') ? 'active' : '' }}">
                    <x-icon name="document-chart-bar" class="icon"/>
                    <span>Laporan & Berita Acara</span>
                </a>
            @endif
        </nav>

        {{-- User info --}}
        <div class="border-t border-white/10 px-4 py-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                 style="background: hsl(var(--color-primary-light))">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                <div class="text-white/40 text-xs truncate">{{ auth()->user()->primaryRole()->label() }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-white/40 hover:text-white/80 transition-colors" title="Keluar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
         class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm sm:hidden"></div>

    {{-- ════════════════════ MAIN ════════════════════ --}}
    <div class="main-content">

        {{-- Top bar --}}
        <header class="topbar">
            <div class="flex items-center gap-4">
                {{-- Mobile hamburger --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="sm:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div>
                    <h1 class="topbar-title">{{ $title ?? 'Dashboard' }}</h1>
                    @isset($breadcrumb)
                        <nav class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                            {{ $breadcrumb }}
                        </nav>
                    @endisset
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Notifikasi (placeholder) --}}
                <button class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

                {{-- Profile link --}}
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold"
                         style="background: hsl(var(--color-primary-light))">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-sm text-gray-700 hidden sm:block">{{ auth()->user()->name }}</span>
                </a>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-6 mt-4 alert-success" role="alert">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 alert-error" role="alert">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-6 mt-4 alert-error" role="alert">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Page body --}}
        <main class="page-body">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-100 bg-white px-8 py-3 text-xs text-gray-400 flex justify-between">
            <span>© {{ date('Y') }} PKM Universitas Udayana</span>
            <span>Sistem Pengajuan Proposal Internal</span>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
