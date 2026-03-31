<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationSystem;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    /**
     * Nampilin Halaman UI Backup
     */
    public function index()
    {
        return view('backup.index');
    }

    /**
     * Proses Download Database (Jalur Aman - Pure PHP Dumper)
     */
    public function download(Request $request)
    {
        // 1. Nama file backup
        $filename = "Backup_PricingEngine_" . date('Y-m-d_H-i-s') . ".sql";
        $storagePath = storage_path("app/public/" . $filename);

        try {
            // 2. Tulis Header File SQL
            $sqlDump = "-- ===========================================\n";
            $sqlDump .= "-- Backup Database Pricing Engine\n";
            $sqlDump .= "-- Waktu Backup: " . now()->format('Y-m-d H:i:s') . "\n";
            $sqlDump .= "-- ===========================================\n\n";

            // 3. Ambil semua nama tabel di database
            $tables = DB::select('SHOW TABLES');

            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                
                // Ambil struktur tabel (Create Table)
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0]->{'Create Table'};
                $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlDump .= $createTable . ";\n\n";

                // Ambil isi datanya (Baris per baris)
                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array)$row;
                    $values = array_map(function($value) {
                        // Bersihkan data biar ga error kalau ada tanda petik
                        return $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                    }, $rowArray);
                    
                    $sqlDump .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sqlDump .= "\n\n";
            }

            // 4. Simpan ke dalam file .sql di server sementara
            File::put($storagePath, $sqlDump);

            // 5. Nyalain CCTV biar Bos tahu siapa yang download data
            AuditLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'CREATE',
                'module' => 'Backup & Restore',
                'description' => 'Melakukan pencadangan (Backup) seluruh database sistem (Mode Aman). Nama file: ' . $filename,
                'ip_address' => $request->ip()
            ]);

            // 6. Kirim Notif ke Bos
            NotificationSystem::create([
                'type' => 'success',
                'title' => 'Auto-Backup Sistem Selesai',
                'message' => 'Sistem telah berhasil melakukan pencadangan database dengan aman.',
                'icon' => 'fas fa-database'
            ]);

            // 7. Langsung paksa browser download filenya, lalu hapus dari server biar ga menuhin memori
            return response()->download($storagePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Kalau ada apa-apa, ga bakal blank putih, tapi balik bawa pesan error
            return redirect()->back()->with('error', 'Gagal melakukan backup: ' . $e->getMessage());
        }
    }
}