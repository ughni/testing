<?php

namespace App\Http\Controllers;

use App\Models\SupplierContract;
use Illuminate\Http\Request;
// 🔥 WAJIB TAMBAHIN 2 BARIS INI BUAT MANGGIL MESIN HALAMAN 🔥
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class DocumentCenterController extends Controller
{
    public function index($type)
    {
        // 1. Validasi Keamanan
        $validTypes = ['all', 'active', 'expired'];
        if (!in_array($type, $validTypes)) {
            abort(404);
        }

        // 2. Cari versi tertinggi
        $maxVersions = SupplierContract::selectRaw('supplier_id, MAX(contract_version) as max_version')
                        ->groupBy('supplier_id')
                        ->pluck('max_version', 'supplier_id');

        // 3. Tarik data
        $allContracts = SupplierContract::with('supplier')->orderBy('created_at', 'desc')->get();

        // 4. Logika Filter Berdasarkan Tipe
        if ($type === 'active') {
            $contracts = $allContracts->filter(function($contract) use ($maxVersions) {
                return isset($maxVersions[$contract->supplier_id]) && $maxVersions[$contract->supplier_id] == $contract->contract_version;
            });
        } elseif ($type === 'expired') {
            $contracts = $allContracts->filter(function($contract) use ($maxVersions) {
                return isset($maxVersions[$contract->supplier_id]) && $maxVersions[$contract->supplier_id] > $contract->contract_version;
            });
        } else {
            $contracts = $allContracts;
        }

        // 🔥 5. MESIN PAGINATION (MEMOTONG DATA JADI 10 PER HALAMAN) 🔥
        $perPage = 10;
        $page = Paginator::resolveCurrentPage() ?: 1;
        $contracts = new LengthAwarePaginator(
            $contracts->forPage($page, $perPage)->values(),
            $contracts->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        // 6. UI Generator
        $ui = [
            'all' => [
                'title' => 'All Supplier Contracts', 
                'icon' => 'fa-folder', 
                'desc' => 'Semua arsip dokumen kontrak dari seluruh supplier, baik yang masih aktif maupun yang sudah kadaluarsa.', 
                'color' => 'blue'
            ],
            'active' => [
                'title' => 'Active Contracts', 
                'icon' => 'fa-folder-open', 
                'desc' => 'Daftar dokumen kontrak supplier versi terbaru yang saat ini sedang berlaku.', 
                'color' => 'emerald'
            ],
            'expired' => [
                'title' => 'Expired Contracts', 
                'icon' => 'fa-archive', 
                'desc' => 'Arsip dokumen kontrak lama yang sudah tidak berlaku atau sudah diperbarui.', 
                'color' => 'slate'
            ],
        ];

        $pageData = $ui[$type];

        return view('document_center.index', compact('contracts', 'type', 'pageData', 'maxVersions'));
    }
}