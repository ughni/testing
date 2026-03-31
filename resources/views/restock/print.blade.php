<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pesanan (PO) - PT Breyy Creative Group</title>
    <style>
        /* 🔥 UBAH: Font ganti jadi Times New Roman biar resmi banget 🔥 */
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 0;
            padding: 40px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px; /* Digeedin dikit karena Times New Roman karakternya lebih langsing */
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 15px;
            color: #333;
        }
        .divider {
            border-top: 2px solid #000;
            margin-bottom: 20px;
        }
        .info-table {
            margin-bottom: 20px;
            font-size: 15px;
        }
        .info-table td {
            padding: 4px 0;
        }
        .info-table td.label {
            width: 130px;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px; /* Disesuaikan biar tetep muat tapi kebaca jelas */
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f3f4f6;
        }
        .data-table td.center { text-align: center; }
        .data-table td.right { text-align: right; }
        
        .supplier-address {
            font-size: 12px;
            color: #444;
            margin-top: 3px;
        }

        .grand-total-row td {
            font-weight: bold;
        }
        .grand-total-label {
            text-align: right;
            padding-right: 15px !important;
        }
        .note {
            font-style: italic;
            font-size: 14px;
            margin-bottom: 60px;
        }
        .signatures {
            width: 100%;
            margin-top: 50px;
            table-layout: fixed;
        }
        .signatures td {
            text-align: center;
            vertical-align: bottom;
            font-size: 15px;
        }
        .signatures .sign-space {
            height: 100px; 
        }
        .signatures .name {
            font-weight: bold;
            text-decoration: underline; 
        }
        
        /* Tombol Print */
        .no-print { margin-bottom: 20px; text-align: right; }
        .btn-print { background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-family: Arial, sans-serif; /* Tombol tetep Arial biar UI-nya modern */}
        .btn-print:hover { background: #1d4ed8; }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Surat Pesanan</button>
    </div>

    <div class="header">
        <h1>SURAT PESANAN BARANG (PO)</h1>
        <p><strong>PT. Breyy Creative Group</strong></p>
        <p>Kajen, Kabupaten Pekalongan, Jawa Tengah</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td class="label">Tanggal Cetak</td>
            <td>: {{ date('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td>: Daftar Pembelian Barang (Restock Gudang)</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 15%;">Supplier</th>
                <th style="width: 20%;">Alamat Supplier</th>
                <th style="width: 10%;">Qty Beli</th>
                <th style="width: 12%;">Harga Total</th>
                <th style="width: 13%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            
            @foreach($plans as $index => $plan)
                @php 
                    $grandTotal += $plan->price; 
                    $supInfo = \App\Models\Supplier::where('nama_supplier', $plan->supplier_name)->first();
                    $alamat = $supInfo ? $supInfo->alamat : '-';
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $plan->product_name }}</strong></td>
                    <td>{{ $plan->supplier_name }}</td>
                    <td><div class="supplier-address">{{ $alamat }}</div></td>
                    <td class="center">{{ $plan->qty }} {{ $plan->unit ?? 'Pcs' }}</td>
                    <td class="right">Rp {{ number_format($plan->price, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endforeach
            
            <tr class="grand-total-row">
                <td colspan="5" class="grand-total-label">ESTIMASI GRAND TOTAL PEMBAYARAN</td>
                <td class="right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <p class="note">*Catatan untuk Driver/Supir: Beri tanda centang / coret di kolom Keterangan jika ada barang yang kosong di pabrik/toko.</p>

    <table class="signatures">
        <tr>
            <td>Dibuat Oleh,</td>
            <td>Diperiksa Oleh / Driver,</td>
            <td>Disetujui Oleh,</td>
        </tr>
        <tr>
            <td class="sign-space"></td>
            <td class="sign-space"></td>
            <td class="sign-space"></td>
        </tr>
        <tr>
            <td class="name">( Admin Gudang )</td>
            <td class="name">( ............................ )</td>
            <td class="name">( Direktur )</td>
        </tr>
    </table>

</body>
</html>