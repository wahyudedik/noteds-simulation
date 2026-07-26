<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    <title>Syarat & Ketentuan — {{ config('app.name') }}</title>
    <meta name="description" content="Syarat dan Ketentuan penggunaan {{ config('app.name') }} - Pelajari aturan dan ketentuan yang berlaku saat menggunakan Layanan kami.">
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpeg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    @include('components.app-header')

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Syarat & Ketentuan</h1>
        <p class="text-sm text-gray-400 mb-8">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>

        <div class="prose prose-gray max-w-none space-y-8">

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Penerimaan Syarat</h2>
                <p class="text-gray-600 leading-relaxed">
                    Dengan mengakses atau menggunakan {{ config('app.name') }} ("Layanan"), Anda setuju untuk terikat oleh
                    Syarat dan Ketentuan ini. Jika Anda tidak setuju dengan syarat-syarat ini, mohon untuk tidak menggunakan
                    Layanan kami.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">2. Kelayakan</h2>
                <p class="text-gray-600 leading-relaxed">
                    Anda harus berusia minimal 13 tahun (atau usia minimum yang berlaku di yurisdiksi Anda) untuk menggunakan
                    Layanan ini. Dengan menggunakan Layanan, Anda menjamin bahwa Anda memenuhi kelayakan usia tersebut.
                    Jika Anda berusia di bawah 18 tahun, Anda memerlukan izin orang tua atau wali untuk menggunakan Layanan.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">3. Pendaftaran Akun</h2>
                <ul class="list-disc list-inside text-gray-600 space-y-1.5">
                    <li>Anda harus memberikan informasi yang akurat dan lengkap saat mendaftar.</li>
                    <li>Anda bertanggung jawab menjaga kerahasiaan kredensial akun Anda.</li>
                    <li>Anda bertanggung jawab atas semua aktivitas yang terjadi di akun Anda.</li>
                    <li>Anda tidak boleh membagikan akun Anda dengan pihak lain atau menggunakan akun orang lain.</li>
                    <li>Segera laporkan kepada kami jika Anda mendeteksi penggunaan akun yang tidak sah.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">4. Konten Pengguna</h2>

                <h3 class="text-lg font-medium text-gray-800 mt-4 mb-2">4.1 Kepemilikan Konten</h3>
                <p class="text-gray-600 leading-relaxed">
                    Anda mempertahankan kepemilikan atas konten yang Anda buat dan unggah ke Layanan ("Konten Pengguna").
                    Dengan mengunggah konten, Anda memberikan {{ config('app.name') }} lisensi non-eksklusif, dapat ditransfer,
                    dan dapat disublisensikan untuk menggunakan, mereproduksi, mendistribusikan, dan menampilkan konten tersebut
                    sehubungan dengan penyediaan Layanan.
                </p>

                <h3 class="text-lg font-medium text-gray-800 mt-4 mb-2">4.2 Tanggung Jawab Konten</h3>
                <p class="text-gray-600 leading-relaxed">Anda bertanggung jawab penuh atas konten yang Anda unggah. Anda menjamin bahwa:</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1.5 mt-2">
                    <li>Konten adalah karya original Anda atau Anda memiliki hak untuk membagikannya.</li>
                    <li>Konten tidak melanggar hak cipta, hak paten, merek dagang, atau hak kekayaan intelektual lainnya.</li>
                    <li>Konten tidak mengandung malware, virus, kode berbahaya, atau komponen yang merugikan.</li>
                    <li>Konten tidak melanggar hukum atau peraturan yang berlaku.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Larangan Penggunaan</h2>
                <p class="text-gray-600 leading-relaxed">Anda dilarang untuk:</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1.5 mt-2">
                    <li>Menggunakan Layanan untuk tujuan ilegal atau tidak sah.</li>
                    <li>Mengunggah konten yang mengandung malware, virus, atau kode berbahaya.</li>
                    <li>Mencoba mengakses area yang tidak diizinkan atau sistem Layanan.</li>
                    <li>Mengganggu atau merusak Layanan atau server yang terhubung ke Layanan.</li>
                    <li>Melakukan scraping, crawling, atau pengambilan data otomatis tanpa izin tertulis.</li>
                    <li>Meniru atau meniru orang atau entitas lain.</li>
                    <li>Menggunakan Layanan untuk mengirim spam, phishing, atau komunikasi yang tidak diinginkan.</li>
                    <li>Membuat banyak akun palsu atau menggunakan bot.</li>
                    <li>Menjual, mentransfer, atau memberikan akses akun Anda kepada pihak ketiga tanpa izin kami.</li>
                    <li>Menggunakan Layanan untuk menayangkan iklan yang tidak sah atau melanggar kebijakan.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Program Kreator & Monetisasi</h2>

                <h3 class="text-lg font-medium text-gray-800 mt-4 mb-2">6.1 Kelayakan Kreator</h3>
                <p class="text-gray-600 leading-relaxed">
                    Untuk berpartisipasi dalam Program Kreator, Anda harus memenuhi persyaratan yang ditetapkan, termasuk
                    namun tidak terbatas pada reputasi minimum, jumlah followers, dan kepatuhan terhadap kebijakan konten.
                </p>

                <h3 class="text-lg font-medium text-gray-800 mt-4 mb-2">6.2 Pembagian Pendapatan</h3>
                <p class="text-gray-600 leading-relaxed">
                    Pendapatan dari iklan dibagikan sesuai dengan persentase yang ditetapkan oleh {{ config('app.name') }}.
                    Kami berhak mengubah persentase pembagian dengan pemberitahuan sebelumnya. Minimum payout adalah Rp 500.000.
                </p>

                <h3 class="text-lg font-medium text-gray-800 mt-4 mb-2">6.3 Pemrosesan Pembayaran</h3>
                <p class="text-gray-600 leading-relaxed">
                    Pembayaran akan diproses melalui metode yang tersedia (transfer bank, e-wallet). Anda bertanggung jawab
                    untuk memastikan informasi pembayaran yang diberikan akurat dan lengkap.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Iklan</h2>
                <p class="text-gray-600 leading-relaxed">
                    Layanan ini didukung oleh iklan, termasuk melalui Google AdSense dan mitra iklan lainnya. Anda menyetujui
                    penayangan iklan sebagai bagian dari penggunaan Layanan. Kami tidak bertanggung jawab atas konten iklan
                    yang ditampilkan oleh pihak ketiga. Untuk informasi lebih lanjut, lihat Kebijakan Iklan Google di
                    <a href="https://policies.google.com/technologies/ads" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">policies.google.com</a>.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">8. Hak Kekayaan Intelektual</h2>
                <p class="text-gray-600 leading-relaxed">
                    Semua hak kekayaan intelektual dalam Layanan, termasuk namun tidak terbatas pada desain, logo, kode sumber,
                    dan fitur, adalah milik {{ config('app.name') }} atau pemberi lisensinya. Anda tidak diberikan hak untuk
                    menyalin, memodifikasi, mendistribusikan, atau membuat karya turunan dari Layanan tanpa izin tertulis.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">9. Penafian Jaminan</h2>
                <p class="text-gray-600 leading-relaxed">
                    Layanan disediakan "sebagaimana adanya" dan "sebagaimana tersedia" tanpa jaminan apa pun, baik tersurat
                    maupun tersirat. Kami tidak menjamin bahwa Layanan akan selalu tersedia, aman, atau bebas dari kesalahan.
                    Kami tidak bertanggung jawab atas kerugian yang timbul dari penggunaan atau ketidakmampuan menggunakan Layanan.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">10. Batasan Tanggung Jawab</h2>
                <p class="text-gray-600 leading-relaxed">
                    Sejauh diizinkan oleh hukum yang berlaku, {{ config('app.name') }} tidak akan bertanggung jawab atas
                    kerugian tidak langsung, insidental, khusus, konsekuensial, atau hukuman, termasuk namun tidak terbatas
                    pada kehilangan data, pendapatan, atau keuntungan, yang timbul dari penggunaan Layanan.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">11. Ganti Rugi</h2>
                <p class="text-gray-600 leading-relaxed">
                    Anda setuju untuk mengganti rugi dan membebaskan {{ config('app.name') }} dari klaim, kerugian, biaya,
                    atau pengeluaran (termasuk biaya pengacara yang wajar) yang timbul dari pelanggaran Syarat dan Ketentuan
                    ini atau penggunaan Layanan Anda.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">12. Penghentian Akun</h2>
                <p class="text-gray-600 leading-relaxed">
                    Kami berhak menangguhkan atau menghapus akun Anda tanpa pemberitahuan sebelumnya jika Anda melanggar
                    Syarat dan Ketentuan ini. Anda juga dapat menghapus akun Anda kapan saja melalui pengaturan profil.
                    Setelah penghentian, hak Anda untuk menggunakan Layanan langsung berakhir.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">13. Hukum yang Berlaku</h2>
                <p class="text-gray-600 leading-relaxed">
                    Syarat dan Ketentuan ini tunduk pada hukum Republik Indonesia. Setiap sengketa yang timbul dari atau
                    sehubungan dengan Syarat dan Ketentuan ini akan diselesaikan melalui pengadilan yang berwenang di
                    Indonesia.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">14. Perubahan Syarat</h2>
                <p class="text-gray-600 leading-relaxed">
                    Kami berhak mengubah Syarat dan Ketentuan ini kapan saja. Perubahan akan dipublikasikan di halaman ini
                    dengan tanggal "Terakhir diperbarui" yang diperbarui. Untuk perubahan material, kami akan memberikan
                    notifikasi melalui email atau banner di situs web. Penggunaan Layanan secara berkelanjutan setelah
                    perubahan berlaku merupakan persetujuan Anda terhadap syarat yang diperbarui.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">15. Hubungi Kami</h2>
                <p class="text-gray-600 leading-relaxed">
                    Jika Anda memiliki pertanyaan mengenai Syarat dan Ketentuan ini, silakan hubungi kami:
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-1.5 mt-2">
                    <li><strong>Email:</strong> <a href="mailto:info@noteds.com" class="text-blue-600 hover:underline">info@noteds.com</a></li>
                    <li><strong>Situs Web:</strong> <a href="{{ config('app.url') }}" class="text-blue-600 hover:underline">{{ config('app.url') }}</a></li>
                </ul>
            </section>

        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo.jpeg') }}" alt="Noteds" class="w-6 h-6 rounded object-cover" />
                    <span class="font-semibold text-gray-900">Noteds</span>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-gray-700 transition">Kebijakan Privasi</a>
                    <a href="{{ route('terms-of-service') }}" class="hover:text-gray-700 transition">Syarat & Ketentuan</a>
                    <span>&copy; {{ date('Y') }}</span>
                </div>
            </div>
        </div>
    </footer>

    <x-toast />
</body>
</html>
