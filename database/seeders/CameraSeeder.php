<?php

namespace Database\Seeders;

use App\Models\Camera;
use Illuminate\Database\Seeder;

class CameraSeeder extends Seeder
{
    public function run(): void
    {
        // [nama, merek, harga 24 jam, deskripsi (berisi daftar tarif), foto]
        $data = [
            [
                'Canon EOS M10',
                'Canon',
                100000,
                "Kamera mirrorless ringan dengan layar flip.\n"
                . "Tarif: 30.000 / 3 jam, 50.000 / 6 jam, 65.000 / 8 jam, 80.000 / 12 jam, 100.000 / 24 jam.",
                'canon-eos-m10.jpg',
            ],
            [
                'Sony Cyber-shot W320',
                'Sony',
                70000,
                "Kamera pocket digital yang ringkas dan praktis dibawa.\n"
                . "Tarif: 25.000 / 6 jam, 50.000 / 12 jam, 70.000 / 24 jam, 120.000 / 48 jam, 55.000 / 3 hari atau lebih.",
                'sony-cybershot-w320.jpg',
            ],
            [
                'Kodak Pixpro FZ55',
                'Kodak',
                80000,
                "Kamera pocket digital dengan body hitam yang simpel.\n"
                . "Tarif: 30.000 / 6 jam, 60.000 / 12 jam, 80.000 / 24 jam.",
                'kodak-pixpro-fz55.jpg',
            ],
        ];

        foreach ($data as [$nama, $merek, $harga, $desk, $foto]) {
            Camera::updateOrCreate(
                ['nama' => $nama],
                [
                    'merek'          => $merek,
                    'harga_per_hari' => $harga,
                    'deskripsi'      => $desk,
                    'foto'           => $foto,
                    'status'         => 'tersedia',
                ]
            );
        }
    }
}