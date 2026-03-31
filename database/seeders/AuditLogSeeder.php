<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditLog;
use Carbon\Carbon;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            [
                'user_id' => 1, // Asumsi ID 1 itu Super Admin
                'action' => 'LOGIN',
                'module' => 'Authentication',
                'description' => 'Super Administrator berhasil login ke dalam sistem.',
                'ip_address' => '192.168.1.10',
                'created_at' => Carbon::now()->subDays(2)->addHours(8),
            ],
            [
                'user_id' => 2, // Asumsi ID 2 itu Manager
                'action' => 'UPDATE',
                'module' => 'Master Produk',
                'description' => 'Mengubah HPP produk "Gula Pasir 1Kg" dari Rp 14.500 menjadi Rp 15.200 karena kenaikan harga pasar.',
                'ip_address' => '192.168.1.24',
                'created_at' => Carbon::now()->subDays(1)->addHours(10),
            ],
            [
                'user_id' => 3, // Asumsi ID 3 itu Admin Staff
                'action' => 'CREATE',
                'module' => 'Document Center',
                'description' => 'Mengunggah kontrak baru (V-1) untuk supplier "PT Makmur Jaya Sentosa".',
                'ip_address' => '192.168.1.45',
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'user_id' => 1,
                'action' => 'UPDATE',
                'module' => 'Formula Settings',
                'description' => 'Mengubah Margin Profit Minimum untuk Kategori "Sembako" dari 10% menjadi 12%.',
                'ip_address' => '192.168.1.10',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'user_id' => 1,
                'action' => 'DELETE',
                'module' => 'Supplier Management',
                'description' => 'Menghapus data supplier "CV Fiktif Abadi" dari sistem secara permanen.',
                'ip_address' => '192.168.1.10',
                'created_at' => Carbon::now()->subMinutes(30),
            ],
        ];

        foreach ($logs as $log) {
            AuditLog::create($log);
        }
    }
}