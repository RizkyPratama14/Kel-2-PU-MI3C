<?php

namespace Database\Seeders;

use App\Models\Camera;
use Illuminate\Database\Seeder;

class CameraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
        ['Canon EOS M50', 'Canon', 150000, 'Mirrorless ringan, cocok untuk vlog dan foto harian.', 'canon-m50.jpg'],
        ['Sony A6000', 'Sony', 160000, 'Autofokus cepat, favorit pemula.', 'sony-a6000.jpg'],
        ['Fujifilm X-T200', 'Fujifilm', 180000, 'Warna khas Fujifilm, body stylish.', 'fuji-xt200.jpg'],
        ['Canon 700D', 'Canon', 120000, 'DSLR klasik untuk belajar fotografi.', 'canon-700d.jpg'],
        ['Nikon D5600', 'Nikon', 140000, 'DSLR dengan layar flip dan hasil tajam.', 'nikon-d5600.jpg'],
        ['Sony ZV-E10', 'Sony', 200000, 'Kamera vlog dengan mikrofon yang bagus.', 'sony-zve10.jpg'],
    ];

    foreach ($data as [$nama, $merek, $harga, $desk, $foto]) {
        Camera::create([
            'nama' => $nama, 'merek' => $merek, 'harga_per_hari' => $harga,
            'deskripsi' => $desk, 'foto' => $foto, 'status' => 'tersedia',
        ]);
    }
    }
}
