<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Program Kreator — Noteds</title>
    <meta name="description" content="Bergabung sebagai kreator Noteds. Buat simulasi interaktif, bagikan ilmu, dan berpeluang memperoleh penghasilan.">

    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .hero-gradient-creator {
            background: linear-gradient(135deg, #2d1b69 0%, #111827 50%, #1e293b 100%);
        }
        .tier-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }
        .step-number {
            background: linear-gradient(135deg, #6366f1, #3b82f6);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    @include('components.app-header')

    {{-- Hero Section --}}
    <div class="hero-gradient-creator text-white py-16 md:py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 text-sm text-purple-200 mb-6">
                <span class="text-lg">💜</span> Program Kreator
            </div>
            <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
                Buat Simulasi. Bagikan Ilmu.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-300 to-blue-300">Bangun Masa Depan.</span>
            </h1>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto mb-8">
                Noteds bukan hanya tempat belajar — tempat para guru, dosen, mahasiswa, pelajar, dan kreator edukasi berbagi simulasi interaktif kepada dunia.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                @auth
                    @if(auth()->user()->isCreator())
                        <a href="{{ route('studio.dashboard') }}" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                            Buka Studio →
                        </a>
                    @else
                        <form action="{{ route('become-creator') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                                Mulai Menjadi Kreator →
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                        Daftar & Mulai Berkreasi →
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl font-semibold text-white transition">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Apa itu Noteds Creator? --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Apa itu Noteds Creator?</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Noteds Creator adalah program bagi siapa saja yang ingin membuat simulasi edukasi interaktif menggunakan HTML dan membagikannya kepada jutaan pengguna melalui Noteds.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Buat Simulasi</h3>
                    <p class="text-sm text-gray-500">Kembangkan simulasi interaktif menggunakan HTML, CSS, dan JavaScript.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Bagikan ke Dunia</h3>
                    <p class="text-sm text-gray-500">Publikasikan simulasi dan jangkau jutaan pengguna di seluruh Indonesia.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Pantau Performa</h3>
                    <p class="text-sm text-gray-500">Dashboard analitik lengkap untuk memantau views, plays, likes, dan penghasilan.</p>
                </div>
            </div>
        </section>

        {{-- Bagaimana Cara Kerjanya? --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Bagaimana Cara Kerjanya?</h2>
                <p class="text-gray-600 text-lg">Enam langkah sederhana untuk memulai perjalananmu sebagai kreator.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach([
                    ['title' => 'Buat Simulasi Interaktif', 'desc' => 'Kembangkan simulasi edukasi menggunakan HTML, CSS, dan JavaScript. Unggah file ZIP melalui Simulation Studio.'],
                    ['title' => 'Upload ke Simulation Studio', 'desc' => 'Upload paket simulasi, tambahkan judul, deskripsi, kategori, dan thumbnail yang menarik.'],
                    ['title' => 'Review oleh Tim Noteds', 'desc' => 'Simulasi akan melalui auto-scan keamanan dan review oleh tim kami untuk memastikan kualitas.'],
                    ['title' => 'Publikasikan ke Dunia', 'desc' => 'Setelah disetujui, simulasi dipublikasikan dan dapat ditemukan oleh jutaan pengguna.'],
                    ['title' => 'Pengguna Memainkan Simulasi', 'desc' => 'Simulasimu dimainkan, disukai, dibagikan, dan diikuti oleh komunitas belajar.'],
                    ['title' => 'Pantau & Kembangkan', 'desc' => 'Gunakan dashboard analitik untuk memantau performa dan terus tingkatkan kualitas karyamu.'],
                ] as $i => $step)
                    <div class="flex gap-4 items-start bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <div class="step-number w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0">
                            {{ $i + 1 }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $step['title'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Program Monetisasi --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Program Monetisasi</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Noteds menyediakan program apresiasi bagi kreator berdasarkan performa iklan yang ditampilkan dalam simulasi.
                    Semakin banyak pengguna memainkan simulasi yang menyertakan iklan, semakin besar potensi penghasilanmu.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-blue-50">
                    <h3 class="font-semibold text-gray-900 text-lg">Revenue Sharing Tiers</h3>
                    <p class="text-sm text-gray-500 mt-1">Persentase penghasilan yang kamu terima meningkat seiring reputasi dan kualitas karyamu.</p>
                </div>
                <div class="grid md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                    @foreach([
                        ['tier' => 'Basic', 'share' => '55%', 'desc' => 'Creator baru', 'color' => 'gray', 'bg' => 'bg-gray-50'],
                        ['tier' => 'Verified', 'share' => '65%', 'desc' => '10+ simulasi, rating ≥ 4.0', 'color' => 'blue', 'bg' => 'bg-blue-50'],
                        ['tier' => 'Expert', 'share' => '75%', 'desc' => '50+ simulasi, rating ≥ 4.5', 'color' => 'purple', 'bg' => 'bg-purple-50'],
                        ['tier' => 'Platinum', 'share' => '85%', 'desc' => '100+ simulasi, rating ≥ 4.7', 'color' => 'yellow', 'bg' => 'bg-amber-50'],
                    ] as $t)
                        <div class="p-6 text-center {{ $t['bg'] }}">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">{{ $t['tier'] }}</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $t['share'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">kreator share</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $t['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm text-amber-800">
                    <strong>Catatan:</strong> Penghasilan kreator berasal dari bagi hasil iklan (ad revenue sharing), bukan langsung dari jumlah play. Besaran penghasilan bergantung pada jumlah impression iklan, CPM, kualitas simulasi, kategori, wilayah, serta kebijakan program yang berlaku. Minimum payout adalah Rp 500.000.
                </div>
            </div>
        </section>

        {{-- Siapa yang Bisa Menjadi Kreator? --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Siapa yang Bisa Menjadi Kreator?</h2>
                <p class="text-gray-600 text-lg">Siapa pun yang ingin berbagi ilmu melalui simulasi interaktif.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['👨‍🏫 Guru', '🎓 Dosen', '📚 Mahasiswa', '🎒 Pelajar', '💻 Programmer', '🎨 Desainer', '🔬 Peneliti', '🧪 Komunitas STEM', '✨ Siapa pun yang ingin berbagi ilmu'] as $persona)
                    <span class="px-5 py-2.5 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-700 shadow-sm hover:shadow-md transition">
                        {{ $persona }}
                    </span>
                @endforeach
            </div>
        </section>

        {{-- Dashboard Kreator --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Dashboard Kreator</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Setiap kreator memiliki dashboard lengkap untuk memantau dan mengelola simulasi mereka.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    ['icon' => '👁️', 'label' => 'Total Views'],
                    ['icon' => '▶️', 'label' => 'Total Plays'],
                    ['icon' => '❤️', 'label' => 'Total Likes'],
                    ['icon' => '👥', 'label' => 'Total Followers'],
                    ['icon' => '🔖', 'label' => 'Total Bookmarks'],
                    ['icon' => '🔗', 'label' => 'Total Shares'],
                    ['icon' => '💰', 'label' => 'Total Penghasilan'],
                    ['icon' => '📊', 'label' => 'Grafik Performa'],
                    ['icon' => '🏆', 'label' => 'Simulasi Terpopuler'],
                    ['icon' => '💬', 'label' => 'Komentar Pengguna'],
                    ['icon' => '📈', 'label' => 'Revenue Analytics'],
                    ['icon' => '🛡️', 'label' => 'Creator Reputation'],
                ] as $item)
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
                        <span class="text-2xl">{{ $item['icon'] }}</span>
                        <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Mengapa Menjadi Kreator? --}}
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mengapa Menjadi Kreator di Noteds?</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach([
                    ['icon' => '🌍', 'title' => 'Karyamu Dipelajari Banyak Orang', 'desc' => 'Simulasimu dapat diakses oleh jutaan pengguna dari seluruh Indonesia.'],
                    ['icon' => '📁', 'title' => 'Bangun Portofolio Edukasi', 'desc' => 'Kumpulkan karya simulasi terbaikmu dalam satu profil kreator yang profesional.'],
                    ['icon' => '👥', 'title' => 'Dapatkan Pengikut', 'desc' => 'Bangun komunitas pengikut yang setia mengikuti karya-karyamu.'],
                    ['icon' => '💰', 'title' => 'Berpeluang Memperoleh Penghasilan', 'desc' => 'Melalui program ad revenue sharing, karya terbaikmu dapat menghasilkan pendapatan.'],
                    ['icon' => '🎓', 'title' => 'Berkontribusi untuk Pendidikan', 'desc' => 'Setiap simulasi yang kamu buat membantu siswa memahami konsep secara interaktif.'],
                    ['icon' => '🏅', 'title' => 'Dapatkan Badge & Sertifikasi', 'desc' => 'Raih badge penghargaan dan sertifikasi kreator untuk meningkatkan reputasi dan tier revenue-mu.'],
                ] as $item)
                    <div class="flex gap-4 items-start bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <span class="text-3xl shrink-0">{{ $item['icon'] }}</span>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $item['title'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Visi Jangka Panjang --}}
        <section class="mb-20">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 md:p-12 text-white text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-6">Visi Jangka Panjang</h2>
                <p class="text-gray-300 text-lg max-w-3xl mx-auto mb-8 leading-relaxed">
                    Kami ingin membangun ekosistem simulasi edukasi terbesar, tempat jutaan pengguna belajar melalui simulasi interaktif dan ribuan kreator berbagi karya terbaik mereka.
                </p>
                <div class="grid md:grid-cols-3 gap-6 max-w-3xl mx-auto text-left">
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-white/10">
                        <p class="text-gray-400 text-sm line-through mb-2">Bukan hanya menonton materi.</p>
                        <p class="text-gray-400 text-sm line-through mb-3">Bukan hanya membaca teori.</p>
                        <p class="text-white font-medium">Belajar dengan mencoba, mengeksplorasi, dan memahami konsep secara langsung melalui simulasi interaktif.</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-white/10">
                        <p class="text-purple-300 font-medium mb-2">Rumah bagi Kreator Simulasi</p>
                        <p class="text-gray-400 text-sm">Seperti YouTube menjadi rumah bagi kreator video, Noteds menjadi rumah bagi kreator simulasi edukasi.</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-white/10">
                        <p class="text-blue-300 font-medium mb-2">Pengalaman Belajar yang Berbeda</p>
                        <p class="text-gray-400 text-sm">Konten utama Noteds adalah pengalaman belajar yang interaktif, bukan sekadar tontonan.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Siap Berkreasi?</h2>
            <p class="text-gray-600 text-lg mb-8">Mulai perjalananmu sebagai kreator simulasi edukasi hari ini.</p>
            <div class="flex flex-wrap justify-center gap-4">
                @auth
                    @if(auth()->user()->isCreator())
                        <a href="{{ route('studio.dashboard') }}" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                            Buka Studio →
                        </a>
                    @else
                        <form action="{{ route('become-creator') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                                Mulai Menjadi Kreator →
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-semibold text-white transition shadow-lg shadow-purple-500/25">
                        Daftar Gratis Sekarang →
                    </a>
                @endauth
            </div>
            <p class="text-gray-400 text-xs mt-6">
                <em>💜 Buat Simulasi. Bagikan Ilmu. Bangun Masa Depan.</em>
            </p>
        </section>

    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-100 mt-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-sm text-gray-400">
            <p>© {{ date('Y') }} {{ config('app.name', 'Noteds') }}. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
