<?php

namespace Database\Seeders;

use App\Models\ExperienceTemplate;
use Illuminate\Database\Seeder;

class ExperienceTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Fisika Dasar',
                'slug' => 'fisika-dasar',
                'description' => 'Simulasi interaktif untuk belajar hukum Newton dengan slider parameter dan grafik.',
                'category' => 'physics',
                'schema' => [
                    'components' => [
                        [
                            'type' => 'text',
                            'label' => 'Judul',
                            'default' => [
                                'content' => 'Hukum Newton: F = m × a',
                                'tag' => 'h2',
                                'fontSize' => 'text-2xl',
                                'color' => '#1e40af',
                                'align' => 'center',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'label' => 'Penjelasan',
                            'default' => [
                                'content' => 'Geser slider untuk mengubah massa dan gaya, lalu lihat hasil percepatan pada grafik.',
                                'tag' => 'p',
                                'fontSize' => 'text-base',
                                'color' => '#374151',
                                'align' => 'left',
                            ],
                        ],
                        [
                            'type' => 'slider',
                            'label' => 'Massa (kg)',
                            'default' => [
                                'label' => 'Massa',
                                'min' => 1,
                                'max' => 100,
                                'step' => 1,
                                'defaultValue' => 10,
                                'unit' => ' kg',
                            ],
                        ],
                        [
                            'type' => 'slider',
                            'label' => 'Gaya (N)',
                            'default' => [
                                'label' => 'Gaya',
                                'min' => 1,
                                'max' => 500,
                                'step' => 5,
                                'defaultValue' => 50,
                                'unit' => ' N',
                            ],
                        ],
                        [
                            'type' => 'chart',
                            'label' => 'Grafik Percepatan',
                            'default' => [
                                'title' => 'Percepatan vs Massa',
                                'type' => 'line',
                                'labels' => '1, 5, 10, 20, 50, 100',
                                'values' => '50, 10, 5, 2.5, 1, 0.5',
                                'color' => '#3b82f6',
                            ],
                        ],
                    ],
                ],
                'default_config' => [
                    'settings' => [
                        'theme' => 'light',
                        'showHeader' => true,
                    ],
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Kuis Interaktif',
                'slug' => 'kuis-interaktif',
                'description' => 'Buat kuis multiple choice dengan feedback instan dan penjelasan.',
                'category' => 'education',
                'schema' => [
                    'components' => [
                        [
                            'type' => 'text',
                            'label' => 'Judul Kuis',
                            'default' => [
                                'content' => '📝 Kuis Pengetahuan Umum',
                                'tag' => 'h2',
                                'fontSize' => 'text-2xl',
                                'color' => '#7c3aed',
                                'align' => 'center',
                            ],
                        ],
                        [
                            'type' => 'quiz',
                            'label' => 'Pertanyaan 1',
                            'default' => [
                                'question' => 'Ibukota Indonesia adalah?',
                                'options' => 'Jakarta, Surabaya, Bandung, Medan',
                                'correctIndex' => 0,
                                'explanation' => 'Jakarta adalah ibu kota Indonesia sejak tahun 1945.',
                            ],
                        ],
                        [
                            'type' => 'quiz',
                            'label' => 'Pertanyaan 2',
                            'default' => [
                                'question' => 'Planet terbesar dalam tata surya adalah?',
                                'options' => 'Mars, Jupiter, Saturnus, Neptunus',
                                'correctIndex' => 1,
                                'explanation' => 'Jupiter adalah planet terbesar dengan diameter sekitar 142.984 km.',
                            ],
                        ],
                    ],
                ],
                'default_config' => [
                    'settings' => [
                        'theme' => 'light',
                        'showHeader' => true,
                    ],
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Eksplorasi Visual',
                'slug' => 'eksplorasi-visual',
                'description' => 'Eksplorasi diagram interaktif dengan gambar dan caption.',
                'category' => 'biology',
                'schema' => [
                    'components' => [
                        [
                            'type' => 'text',
                            'label' => 'Judul',
                            'default' => [
                                'content' => '🔬 Eksplorasi Sel',
                                'tag' => 'h2',
                                'fontSize' => 'text-2xl',
                                'color' => '#059669',
                                'align' => 'center',
                            ],
                        ],
                        [
                            'type' => 'image',
                            'label' => 'Diagram Sel',
                            'default' => [
                                'imageUrl' => '',
                                'alt' => 'Diagram sel hewan',
                                'caption' => 'Diagram sel hewan dengan organel-organel penting',
                                'maxWidth' => 'max-w-lg',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'label' => 'Penjelasan',
                            'default' => [
                                'content' => 'Sel adalah unit struktural dan fungsional terkecil dari semua organisme. Sel memiliki membran yang mengelilingi sitoplasma.',
                                'tag' => 'p',
                                'fontSize' => 'text-base',
                                'color' => '#374151',
                                'align' => 'left',
                            ],
                        ],
                        [
                            'type' => 'slider',
                            'label' => 'Zoom Level',
                            'default' => [
                                'label' => 'Zoom',
                                'min' => 1,
                                'max' => 10,
                                'step' => 1,
                                'defaultValue' => 5,
                                'unit' => 'x',
                            ],
                        ],
                    ],
                ],
                'default_config' => [
                    'settings' => [
                        'theme' => 'light',
                        'showHeader' => true,
                    ],
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($templates as $data) {
            ExperienceTemplate::updateOrCreate(
                ['slug' => $data['slug']],
                $data,
            );
        }
    }
}
