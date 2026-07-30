<?php

namespace Database\Seeders;

use App\Models\AdNetworkSetting;
use Illuminate\Database\Seeder;

class AdNetworkSettingSeeder extends Seeder
{
    public function run(): void
    {
        $networks = [
            [
                'network' => 'adsense',
                'display_name' => 'Google AdSense',
                'is_enabled' => true,
                'is_active' => true,
                'allow_banner' => true,
                'allow_native' => true,
                'allow_interstitial' => false,
                'allow_popunder' => false,
                'allow_video' => false,
                'ads_txt_entry' => 'google.com, pub-2771325503977360, DIRECT, f08c47fec0942fa0',
                'estimated_rpm' => 1.50,
                'notes' => 'AdSense utama. Menunggu approval.',
            ],
            [
                'network' => 'monetag',
                'display_name' => 'Monetag',
                'is_enabled' => false,
                'is_active' => true,
                'allow_banner' => true,
                'allow_native' => true,
                'allow_interstitial' => false,
                'allow_popunder' => false,
                'allow_video' => false,
                'estimated_rpm' => 0.50,
                'notes' => 'Hanya gunakan format banner. JANGAN aktifkan pop-under untuk platform edukasi.',
            ],
            [
                'network' => 'propellerads',
                'display_name' => 'PropellerAds',
                'is_enabled' => false,
                'is_active' => true,
                'allow_banner' => true,
                'allow_native' => false,
                'allow_interstitial' => false,
                'allow_popunder' => false,
                'allow_video' => false,
                'estimated_rpm' => 0.30,
                'notes' => 'Hanya format banner. Kualitas iklan bervariasi. Gunakan dengan hati-hati.',
            ],
            [
                'network' => 'media_net',
                'display_name' => 'Media.net',
                'is_enabled' => false,
                'is_active' => true,
                'allow_banner' => true,
                'allow_native' => true,
                'allow_interstitial' => false,
                'allow_popunder' => false,
                'allow_video' => false,
                'estimated_rpm' => 3.00,
                'notes' => 'CPM tinggi, bagus untuk traffic internasional (US/UK). Fokus pada contextual ads.',
            ],
            [
                'network' => 'adsterra',
                'display_name' => 'Adsterra',
                'is_enabled' => false,
                'is_active' => true,
                'allow_banner' => true,
                'allow_native' => true,
                'allow_interstitial' => false,
                'allow_popunder' => false,
                'allow_video' => false,
                'estimated_rpm' => 0.40,
                'notes' => 'Alternatif backup. Mudah approve. Pembayaran via crypto tersedia.',
            ],
            [
                'network' => 'ezoic',
                'display_name' => 'Ezoic',
                'is_enabled' => false,
                'is_active' => true,
                'allow_banner' => true,
                'allow_native' => true,
                'allow_interstitial' => false,
                'allow_popunder' => false,
                'allow_video' => false,
                'estimated_rpm' => 5.00,
                'notes' => 'Target jangka menengah. Butuh minimum 10,000 monthly visits. CPM tertinggi, AI-powered ad placement.',
            ],
        ];

        foreach ($networks as $data) {
            AdNetworkSetting::updateOrCreate(
                ['network' => $data['network']],
                $data
            );
        }
    }
}
