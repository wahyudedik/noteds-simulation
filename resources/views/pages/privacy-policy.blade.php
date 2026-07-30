@php
    $seo = [
        'title' => 'Kebijakan Privasi — ' . config('app.name'),
        'description' => 'Kebijakan Privasi ' . config('app.name') . ' - Pelajari bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.',
        'url' => route('privacy-policy'),
    ];
@endphp

<x-app-layout :$seo>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Kebijakan Privasi</h1>
            <p class="text-sm text-gray-400 dark:text-gray-500 mb-8">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>

            <div class="prose prose-gray dark:prose-invert max-w-none space-y-8">

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">1. Pendahuluan</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ config('app.name') }} ("kami", "kita", atau "milik kami") mengoperasikan situs web {{ config('app.url') }}
                        (selanjutnya disebut "Layanan"). Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan,
                        mengungkapkan, dan menjaga informasi pribadi Anda saat Anda menggunakan Layanan kami.
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-3">
                        Dengan mengakses atau menggunakan Layanan kami, Anda menyetujui pengumpulan dan penggunaan informasi
                        sesuai dengan kebijakan ini. Jika Anda tidak setuju dengan kebijakan ini, mohon untuk tidak menggunakan Layanan kami.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">2. Informasi yang Kami Kumpulkan</h2>

                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mt-4 mb-2">2.1 Informasi yang Anda Berikan</h3>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1.5">
                        <li><strong>Informasi Akun:</strong> Saat mendaftar, kami mengumpulkan nama, alamat email, username, dan informasi profil yang Anda pilih untuk ditampilkan.</li>
                        <li><strong>Konten Buatan Pengguna:</strong> Simulasi, komentar, koleksi, dan konten lain yang Anda unggah atau buat melalui Layanan.</li>
                        <li><strong>Informasi Pembayaran:</strong> Untuk kreator yang menerima pembayaran, kami mengumpulkan informasi rekening bank atau e-wallet yang diperlukan untuk pemrosesan pembayaran.</li>
                    </ul>

                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mt-4 mb-2">2.2 Informasi yang Dikumpulkan Secara Otomatis</h3>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1.5">
                        <li><strong>Data Log:</strong> Alamat IP, jenis browser, sistem operasi, halaman yang dikunjungi, waktu akses, dan durasi kunjungan.</li>
                        <li><strong>Data Perangkat:</strong> Tipe perangkat, resolusi layar, dan pengaturan bahasa.</li>
                        <li><strong>Data Cookie:</strong> Kami menggunakan cookie dan teknologi serupa untuk mengingat preferensi Anda, sesi login, dan analitik.</li>
                    </ul>

                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mt-4 mb-2">2.3 Informasi dari Pihak Ketiga</h3>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1.5">
                        <li><strong>Autentikasi Sosial:</strong> Jika Anda mendaftar melalui Google, kami menerima informasi publik dari akun Google Anda (nama, email, foto profil).</li>
                        <li><strong>Iklan:</strong> Google AdSense dan mitra iklan lainnya dapat mengumpulkan data menggunakan cookie dan teknologi penelusuran. Informasi lebih lanjut tersedia di <a href="https://policies.google.com/technologies/ads" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 hover:underline">Kebijakan Iklan Google</a>.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">3. Penggunaan Informasi</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Kami menggunakan informasi yang dikumpulkan untuk:</p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1.5 mt-2">
                        <li>Menyediakan, memelihara, dan meningkatkan Layanan kami.</li>
                        <li>Memproses registrasi akun dan mengelola autentikasi Anda.</li>
                        <li>Menampilkan konten yang dipersonalisasi dan rekomendasi experience.</li>
                        <li>Memproses pembayaran dan distribusi penghasilan kreator.</li>
                        <li>Mengirimkan notifikasi terkait aktivitas akun, komentar, dan fitur baru.</li>
                        <li>Mendeteksi, mencegah, dan mengatasi penipuan, penyalahgunaan, dan masalah keamanan.</li>
                        <li>Menganalisis penggunaan Layanan untuk perbaikan dan pengembangan produk.</li>
                        <li>Menayangkan iklan yang relevan melalui Google AdSense dan mitra iklan lainnya.</li>
                        <li>Mematuhi kewajiban hukum dan peraturan yang berlaku.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">4. Berbagi Informasi</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Kami tidak menjual informasi pribadi Anda. Kami dapat membagikan informasi dalam situasi berikut:</p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1.5 mt-2">
                        <li><strong>Dengan Pengguna Lain:</strong> Username, foto profil, dan konten publik yang Anda buat dapat dilihat oleh pengguna lain.</li>
                        <li><strong>Dengan Pihak Ketiga:</strong> Penyedia layanan hosting, analitik, dan pemrosesan pembayaran yang membantu operasional kami.</li>
                        <li><strong>Dengan Otoritas Hukum:</strong> Jika diwajibkan oleh hukum, perintah pengadilan, atau proses hukum lainnya.</li>
                        <li><strong>Dalam Transaksi Bisnis:</strong> Jika terjadi penggabungan, akuisisi, atau penjualan aset, informasi Anda dapat ditransfer sebagai bagian dari transaksi tersebut.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">5. Cookie dan Teknologi Pelacakan</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Kami menggunakan cookie untuk:</p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1.5 mt-2">
                        <li><strong>Cookie Esensial:</strong> Diperlukan untuk fungsi dasar situs seperti login dan sesi.</li>
                        <li><strong>Cookie Preferensi:</strong> Mengingat pengaturan dan preferensi Anda.</li>
                        <li><strong>Cookie Analitik:</strong> Membantu kami memahami bagaimana pengguna berinteraksi dengan Layanan.</li>
                        <li><strong>Cookie Iklan:</strong> Digunakan oleh Google AdSense untuk menayangkan iklan yang relevan.</li>
                    </ul>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-3">
                        Anda dapat mengelola cookie melalui pengaturan browser Anda. Namun, menonaktifkan cookie tertentu
                        dapat mempengaruhi fungsionalitas Layanan.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">6. Keamanan Data</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Kami menerapkan langkah-langkah keamanan teknis dan organisasi yang wajar untuk melindungi informasi
                        pribadi Anda dari akses yang tidak sah, pengubahan, pengungkapan, atau penghancuran. Namun, tidak ada
                        metode transmisi internet atau penyimpanan elektronik yang 100% aman, sehingga kami tidak dapat menjamin
                        keamanan absolut.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">7. Hak Anda</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Berdasarkan peraturan perlindungan data yang berlaku (termasuk GDPR dan UU PDP Indonesia), Anda memiliki hak untuk:</p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1.5 mt-2">
                        <li>Mengakses informasi pribadi yang kami miliki tentang Anda.</li>
                        <li>Memperbaiki atau memperbarui informasi yang tidak akurat.</li>
                        <li>Meminta penghapusan informasi pribadi Anda.</li>
                        <li>Menolak atau membatasi pemrosesan informasi tertentu.</li>
                        <li>Meminta portabilitas data dalam format yang dapat dibaca mesin.</li>
                        <li>Menarik persetujuan kapan saja (tanpa mempengaruhi keabsahan pemrosesan sebelumnya).</li>
                    </ul>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-3">
                        Untuk menjalankan hak-hak ini, silakan hubungi kami melalui informasi yang tercantum di bagian "Hubungi Kami".
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">8. Retensi Data</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Kami menyimpan informasi pribadi Anda selama akun Anda aktif atau selama diperlukan untuk menyediakan
                        Layanan. Ketika Anda menghapus akun, kami akan menghapus atau menganonimkan data pribadi Anda dalam
                        waktu 30 hari, kecuali jika harus disimpan lebih lama berdasarkan kewajiban hukum.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">9. Perlindungan Anak</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Layanan kami tidak ditujukan untuk anak di bawah usia 13 tahun (atau usia minimum yang berlaku di
                        yurisdiksi Anda). Kami tidak secara sadar mengumpulkan informasi pribadi dari anak-anak. Jika Anda
                        mengetahui bahwa seorang anak telah memberikan informasi pribadi kepada kami, silakan hubungi kami
                        agar kami dapat menghapus informasi tersebut.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">10. Perubahan Kebijakan</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan akan dipublikasikan di
                        halaman ini dengan tanggal "Terakhir diperubahan" yang diperbarui. Untuk perubahan material, kami akan
                        memberikan notifikasi melalui email atau banner di situs web. Penggunaan Layanan secara berkelanjutan
                        setelah perubahan berlaku merupakan persetujuan Anda terhadap kebijakan yang diperbarui.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">11. Hubungi Kami</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Jika Anda memiliki pertanyaan atau kekhawatiran mengenai Kebijakan Privasi ini, silakan hubungi kami:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1.5 mt-2">
                        <li><strong>Email:</strong> <a href="mailto:info@noteds.com" class="text-blue-600 dark:text-blue-400 hover:underline">info@noteds.com</a></li>
                        <li><strong>Situs Web:</strong> <a href="{{ config('app.url') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ config('app.url') }}</a></li>
                    </ul>
                </section>

            </div>
        </div>
    </div>
</x-app-layout>
