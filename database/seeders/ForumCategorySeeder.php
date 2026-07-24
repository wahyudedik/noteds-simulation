<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Umum',
                'slug' => 'umum',
                'description' => 'Diskusi umum seputar simulasi, pendidikan, dan apapun.',
                'icon' => 'chat-bubble-left',
                'color' => '#6366F1',
                'sort_order' => 1,
            ],
            [
                'name' => 'Bantuan & Pertanyaan',
                'slug' => 'bantuan',
                'description' => 'Ajukan pertanyaan atau minta bantuan terkait penggunaan platform.',
                'icon' => 'question-mark-circle',
                'color' => '#F59E0B',
                'sort_order' => 2,
            ],
            [
                'name' => 'Showcase Simulasi',
                'slug' => 'showcase',
                'description' => 'Pamerkan simulasi buatanmu atau temukan yang menarik dari komunitas.',
                'icon' => 'sparkles',
                'color' => '#10B981',
                'sort_order' => 3,
            ],
            [
                'name' => 'Permintaan Fitur',
                'slug' => 'feature-request',
                'description' => 'Saran dan ide fitur baru untuk platform.',
                'icon' => 'light-bulb',
                'color' => '#3B82F6',
                'sort_order' => 4,
            ],
            [
                'name' => 'Laporan Bug',
                'slug' => 'bug-report',
                'description' => 'Laporkan bug atau masalah yang kamu temukan.',
                'icon' => 'bug-ant',
                'color' => '#EF4444',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            ForumCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
