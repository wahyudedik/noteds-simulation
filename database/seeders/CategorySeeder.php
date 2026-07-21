<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table with default educational categories.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fisika', 'slug' => 'fisika', 'description' => 'Simulasi terkait hukum fisika, mekanika, termodinamika, dan elektromagnetisme.', 'icon' => 'atom', 'sort_order' => 1],
            ['name' => 'Kimia', 'slug' => 'kimia', 'description' => 'Simulasi terkait reaksi kimia, tabel periodik, dan struktur molekul.', 'icon' => 'flask', 'sort_order' => 2],
            ['name' => 'Biologi', 'slug' => 'biologi', 'description' => 'Simulasi terkait organisme, sel, genetika, dan ekosistem.', 'icon' => 'dna', 'sort_order' => 3],
            ['name' => 'Matematika', 'slug' => 'matematika', 'description' => 'Simulasi terkait aljabar, geometri, kalkulus, dan statistik.', 'icon' => 'calculator', 'sort_order' => 4],
            ['name' => 'Ekonomi', 'slug' => 'ekonomi', 'description' => 'Simulasi terkait pasar, inflasi, permintaan penawaran, dan keuangan.', 'icon' => 'chart-bar', 'sort_order' => 5],
            ['name' => 'Sejarah', 'slug' => 'sejarah', 'description' => 'Simulasi interaktif peristiwa sejarah dan kronologi.', 'icon' => 'book-open', 'sort_order' => 6],
            ['name' => 'Geografi', 'slug' => 'geografi', 'description' => 'Simulasi terkait peta, iklim, tata guna lahan, dan bencana alam.', 'icon' => 'globe', 'sort_order' => 7],
            ['name' => 'Informatika', 'slug' => 'informatika', 'description' => 'Simulasi terkait pemrograman, jaringan, dan struktur data.', 'icon' => 'cpu', 'sort_order' => 8],
            ['name' => 'Teknik', 'slug' => 'teknik', 'description' => 'Simulasi terkait rekayasa, mesin, dan struktur bangunan.', 'icon' => 'cog', 'sort_order' => 9],
            ['name' => 'Seni', 'slug' => 'seni', 'description' => 'Simulasi terkait seni visual, musik, dan desain.', 'icon' => 'palette', 'sort_order' => 10],
            ['name' => 'Bahasa', 'slug' => 'bahasa', 'description' => 'Simulasi terkait tata bahasa, kosakata, dan literasi.', 'icon' => 'language', 'sort_order' => 11],
            ['name' => 'Umum', 'slug' => 'umum', 'description' => 'Simulasi dengan topik umum atau multidisiplin.', 'icon' => 'library', 'sort_order' => 12],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
