<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\SystemSetting;
use App\Models\DailyInput; 
use App\Models\User;       
use Carbon\Carbon;         

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Bikin akun Admin (Biar kamu bisa login buat demo)
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin Utama', 'password' => bcrypt('password123')]
        );

        // 1. Tambah Supplier Contoh (Hanya nama saja, karena kontrak ada di tabel terpisah)
        $supplier = Supplier::create([
            'supplier_name' => 'PT Sumber Makmur',
        ]);

        // 2. Tambah Produk Contoh (Tipe Dinamis)
        $beras = Product::create([
            'supplier_id' => $supplier->id,
            'product_name' => 'Beras Premium 5kg', 
            'category' => 'Sembako',               
            'unit' => 'Karung',                    
            'description' => 'Beras super dari supplier makmur', 
            'is_active' => true,
            'price_type' => 'dynamic'              
        ]);

        // 3. Tambah Produk Contoh (Tipe HET)
        $minyak = Product::create([
            'supplier_id' => $supplier->id,
            'product_name' => 'Minyak Kita 1L',
            'category' => 'Sembako',
            'unit' => 'Liter',
            'description' => 'Minyak goreng subsidi',
            'is_active' => true,
            'price_type' => 'HET',
            'het_price' => 14000
        ]);

        // 4. System Settings
        SystemSetting::create([
            'markup_base' => 20.00,        // Untung dasar 20%
            'markup_demand_high' => 15.00, // Tambahan kalau laku keras 15%
            'markup_demand_low' => 10.00,  // Diskon kalau sepi 10%
            'competitor_weight' => 5.00,   // Pengaruh harga lawan
        ]);

        // 5. SUNTIK DATA HARIAN DEMI FITUR "TREND HARGA" BUAT DEMO GMEET!
        $kemarin = Carbon::now()->subDay()->format('Y-m-d');
        $hariIni = Carbon::now()->format('Y-m-d');

        // Skenario A: Harga Beras TETAP (HPP 65rb -> 65rb)
        DailyInput::create([
            'product_id' => $beras->id, 'user_id' => $user->id, 'input_date' => $kemarin,
            'hpp' => 65000, 'hpp_prev' => 65000, 'stock' => 50, 'demand' => 'normal'
        ]);
        DailyInput::create([
            'product_id' => $beras->id, 'user_id' => $user->id, 'input_date' => $hariIni,
            'hpp' => 65000, 'hpp_prev' => 65000, 'stock' => 45, 'demand' => 'normal'
        ]);

        // Skenario B: Harga Minyak NAIK (HPP 11rb -> 12.5rb)
        DailyInput::create([
            'product_id' => $minyak->id, 'user_id' => $user->id, 'input_date' => $kemarin,
            'hpp' => 11000, 'hpp_prev' => 11000, 'stock' => 100, 'demand' => 'tinggi'
        ]);
        DailyInput::create([
            'product_id' => $minyak->id, 'user_id' => $user->id, 'input_date' => $hariIni,
            'hpp' => 12500, 'hpp_prev' => 11000, 'stock' => 80, 'demand' => 'tinggi' 
        ]);

        echo "Data Master & Skenario Trend Harga berhasil ditambahkan, breyy! \n";
    }
}