<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Admin Panel
            </h2>
            <a href="{{ route('admin.simulations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Simulasi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Simulasi</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_simulations'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['published'] }} published &middot; {{ $stats['draft'] }} draft</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Views</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_views']) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Plays</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_plays']) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Likes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_likes']) }}</p>
                </div>
            </div>

            {{-- Forum Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1m0-4V6a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4z" /></svg>
                        </div>
                        <p class="text-sm text-gray-500">Forum Threads</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_forum_threads']) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['unsolved_threads'] }} belum terjawab</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </div>
                        <p class="text-sm text-gray-500">Forum Replies</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_forum_replies']) }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                        </div>
                        <p class="text-sm text-gray-500">Kategori Forum</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_forum_categories'] }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <p class="text-sm text-gray-500">Belum Terjawab</p>
                    </div>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['unsolved_threads'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">butuh perhatian</p>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Kelola Pengguna</div>
                        <div class="text-xs text-gray-500">User, role, creator approval</div>
                    </div>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Kelola Kategori</div>
                        <div class="text-xs text-gray-500">CRUD kategori simulasi</div>
                    </div>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Laporan Pengguna</div>
                        <div class="text-xs text-gray-500">Review & tindak laporan</div>
                    </div>
                </a>
                <a href="{{ route('admin.scans.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Security Scans</div>
                        <div class="text-xs text-gray-500">Auto-scan, sandbox, review</div>
                    </div>
                </a>
                <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Error Logs</div>
                        <div class="text-xs text-gray-500">Lihat & copy error untuk debug</div>
                    </div>
                </a>
                <a href="{{ route('admin.simulations.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Kelola Simulasi</div>
                        <div class="text-xs text-gray-500">CRUD & publish</div>
                    </div>
                </a>
                <a href="{{ route('admin.ads.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Iklan Platform</div>
                        <div class="text-xs text-gray-500">CRUD & manajemen iklan</div>
                    </div>
                </a>
                <a href="{{ route('admin.creator-ads.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Review Iklan Creator</div>
                        <div class="text-xs text-gray-500">Approve, reject, flag iklan</div>
                    </div>
                </a>
                <a href="{{ route('admin.creators.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Manajemen Creator</div>
                        <div class="text-xs text-gray-500">Reputasi, suspend, profil creator</div>
                    </div>
                </a>
                <a href="{{ route('admin.payouts.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Payout Creator</div>
                        <div class="text-xs text-gray-500">Approve, bayar, tolak payout</div>
                    </div>
                </a>
                <a href="{{ route('admin.seo.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">SEO Management</div>
                        <div class="text-xs text-gray-500">Meta tags, structured data, sitemap</div>
                    </div>
                </a>
                <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Platform Analytics</div>
                        <div class="text-xs text-gray-500">Traffic, growth, content performance</div>
                    </div>
                </a>
                <a href="{{ route('admin.ad-analytics.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Ad Analytics</div>
                        <div class="text-xs text-gray-500">Impressions, clicks, CTR, revenue</div>
                    </div>
                </a>
                <a href="{{ route('admin.embeds.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Embed Management</div>
                        <div class="text-xs text-gray-500">Track embed usage & referrers</div>
                    </div>
                </a>
                <a href="{{ route('admin.marketplace.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-lime-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Marketplace</div>
                        <div class="text-xs text-gray-500">Listing, penjualan, revenue</div>
                    </div>
                </a>
                <a href="{{ route('admin.challenges.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Challenges</div>
                        <div class="text-xs text-gray-500">Kompetisi & penilaian kreator</div>
                    </div>
                </a>
                <a href="{{ route('forum.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1m0-4V6a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4z" /></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Forum / Komunitas</div>
                        <div class="text-xs text-gray-500">Threads, replies, moderasi</div>
                    </div>
                </a>
                <a href="{{ route('admin.certifications.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Certification</div>
                        <div class="text-xs text-gray-500">Sertifikasi Verified, Expert, Platinum</div>
                    </div>
                </a>
                <a href="{{ route('admin.sponsors.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Sponsor</div>
                        <div class="text-xs text-gray-500">Data brand & kontak sponsor</div>
                    </div>
                </a>
                <a href="{{ route('admin.sponsorships.index') }}" class="flex items-center gap-3 bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Sponsorship</div>
                        <div class="text-xs text-gray-500">Perjanjian sponsorship & invoice</div>
                    </div>
                </a>
            </div>

            {{-- Recent Simulations --}}
            <div class="bg-white border border-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Simulasi Terbaru</h3>

                    @if($recentSimulations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Judul</th>
                                        <th class="text-left py-3 px-2 text-gray-500 font-medium">Kategori</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Views</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Plays</th>
                                        <th class="text-center py-3 px-2 text-gray-500 font-medium">Status</th>
                                        <th class="text-right py-3 px-2 text-gray-500 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSimulations as $sim)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-2">
                                            <a href="{{ route('admin.simulations.show', $sim) }}" class="font-medium text-gray-900 hover:text-blue-600">
                                                {{ Str::limit($sim->title, 40) }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-2 text-gray-500">{{ $sim->category }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ number_format($sim->view_count) }}</td>
                                        <td class="py-3 px-2 text-center text-gray-500">{{ number_format($sim->play_count) }}</td>
                                        <td class="py-3 px-2 text-center">
                                            @if($sim->is_published)
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Published</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-right">
                                            <a href="{{ route('admin.simulations.edit', $sim) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Edit</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Belum ada simulasi. <a href="{{ route('admin.simulations.create') }}" class="text-blue-600 hover:underline">Upload yang pertama!</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
