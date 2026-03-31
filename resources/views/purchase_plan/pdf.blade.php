<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; color: #555; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .ttd-box { width: 100%; margin-top: 50px; }
        .ttd-box td { text-align: center; width: 33%; vertical-align: bottom; height: 100px; }
    </style>
</head>
<body>

    <div class="header">
        <h1> Purchase Plan (Rencana Pembelian)</h1>
        <p>PT. Breyy Creative Group - Kajen, Jawa Tengah</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Tanggal</strong></td>
            <td width="2%">:</td>
            <td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Perihal</strong></td>
            <td>:</td>
            <td>Surat Pesanan Barang (Purchase Order)</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Barang</th>
                <th width="20%">Supplier</th>
                <th width="10%">Qty Beli</th>
                <th width="15%">Harga Satuan</th>
                <th width="15%">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            
            @forelse($approvedOffers as $index => $item)
                @php 
                    $totalHarga = $item->price * ($item->qty ?? 1);
                    $grandTotal += $totalHarga; 
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->product_name }}</strong></td>
                    <td>{{ $item->supplier_name }}</td>
                    
                    <td class="text-center font-bold">
                        {{ $item->qty ?? 1 }} Pcs
                    </td>
                    
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    
                    <td class="text-right font-bold">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada barang yang divalidasi untuk dibeli hari ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">GRAND TOTAL PEMBAYARAN</th>
                <th class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <br>
    <p><em>*Catatan untuk Supplier: Beri tanda centang / coret jika ada perubahan stok barang langsung di kertas ini.</em></p>

    <table class="ttd-box">
        <tr>
            <td>
                Dibuat Oleh,<br><br><br><br><br>
                ( Admin Gudang )
            </td>
            <td>
                Diperiksa Oleh / Driver,<br><br><br><br><br>
                ( ........................ )
            </td>
            <td>
                Disetujui Oleh,<br><br><br><br><br>
                <strong>( Direktur )</strong>
            </td>
        </tr>
    </table>

</body>
</html>