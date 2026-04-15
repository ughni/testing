<p align="center">
  <div style="background-color: #1e293b; padding: 20px; border-radius: 15px; text-align: center;">
    <h1 style="color: #38bdf8; font-size: 3em; margin-bottom: 0;">🔥 PRICING ENGINE ERP 🔥</h1>
    <p style="color: #94a3b8; font-size: 1.2em;">Smart Inventory, Purchase Plan & Dynamic Pricing System</p>
  </div>
</p>

## 🚀 Tentang Aplikasi

**Pricing Engine ERP** adalah sistem *Enterprise Resource Planning* khusus yang dirancang untuk mengotomatisasi perhitungan Harga Pokok Penjualan (HPP), manajemen Master Produk, dan Rencana Pembelian (Purchase Plan). 

Sistem ini dilengkapi dengan **Kecerdasan Buatan (Auto Adjustment)** yang mampu merespon fluktuasi harga pasar, tingkat *demand*, dan ketersediaan stok secara *real-time*, memastikan perusahaan selalu mendapatkan margin yang optimal tanpa melanggar ketentuan kontrak dari *Supplier*.

## ✨ Fitur Unggulan

- 🧮 **Smart Pricing Calculator:** Perhitungan HPP komprehensif (termasuk *Yield*, Pajak, dan Ongkos Kirim) dengan kalkulasi *Floor Price*, *Base Price*, dan *Median* Kompetitor.
- 🤖 **Auto Adjustment Rules:** Penyesuaian harga otomatis berdasarkan:
  - Inflasi (Kenaikan HPP)
  - Tingkat Permintaan Pasar (*Demand* Tinggi/Rendah)
  - Ketersediaan Stok Kritis (*Panic Buying Protection*)
- 📦 **Purchase Plan & Restock Radar:** Sistem pengajuan *Purchase Order* (PO) otomatis untuk barang kritis, validasi Manajer, dan sinkronisasi penambahan stok langsung ke Master Produk.
- 🤝 **Supplier Contract Manager:** Logika harga yang patuh pada kontrak *Supplier* (Fixed Price, Consignment/Titipan, HET/Harga Eceran Tertinggi, dan Dynamic).
- 🔐 **API Gateway & Webhooks:** Distribusi harga *real-time* ke aplikasi pihak ketiga (seperti POS Kasir atau E-Commerce) menggunakan otentikasi *Secret API Key*.
- 📊 **Reporting:** Fitur cetak PO dalam format PDF, Export/Import Master Data menggunakan Excel, dan sistem *Audit Log* untuk melacak seluruh aktivitas pengguna.

## 🛠️ Tech Stack

- **Framework:** Laravel 10+ / 11
- **Database:** MySQL / PostgreSQL
- **Frontend:** Tailwind CSS, Alpine.js / jQuery, Select2
- **Export/PDF:** DomPDF, Laravel Excel (Maatwebsite)

## ⚙️ Cara Instalasi (Development)

1. Clone repository ini:
   ```bash
   git clone [https://github.com/](https://github.com/)[username-anda]/pricing-engine-erp.git
Masuk ke direktori proyek dan instal dependencies:

Bash
cd pricing-engine-erp
composer install
npm install && npm run build
Salin dan konfigurasi file .env:

Bash
cp .env.example .env
Generate Application Key dan jalankan migrasi database:

Bash
php artisan key:generate
php artisan migrate --seed
Jalankan server lokal:

Bash
php artisan serve
📡 DOKUMENTASI API (Untuk Integrasi Aplikasi Klien)
Sistem ini menyediakan RESTful API untuk mendistribusikan data harga terbaru ke aplikasi klien secara aman.

🔑 Autentikasi
Semua permintaan (Request) ke API WAJIB menyertakan API Key yang valid di dalam Header HTTP. API Key dapat dibuat melalui menu API Integration & Webhooks di dalam dashboard aplikasi ERP.

📍 Endpoint: Ambil Data Harga Barang
Mendapatkan daftar harga jual rekomendasi yang sudah dikalkulasi oleh Pricing Engine.

URL: /api/get-harga-barang

Method: GET

Headers Wajib:

x-api-key: [Token API Anda]

Accept: application/json

💻 Contoh Penggunaan (JavaScript / Fetch API)
Bagi developer aplikasi Klien (misal: POS Kasir), silakan gunakan contoh kode berikut untuk mengambil data dari server ERP:

JavaScript
fetch('[http://domain-erp-anda.com/api/get-harga-barang](http://domain-erp-anda.com/api/get-harga-barang)', {
    method: 'GET',
    headers: {
        // Ganti dengan API Key yang di-generate dari Dashboard ERP
        'x-api-key': 'prc_TokenPanjangAnda1234567890',
        'Accept': 'application/json'
    }
})
.then(response => {
    if (!response.ok) {
        throw new Error('Akses Ditolak! API Key tidak valid atau telah dicabut.');
    }
    return response.json();
})
.then(data => {
    console.log("Data Harga Berhasil Ditarik:", data);
    // Lakukan proses render ke UI Kasir Anda di sini
})
.catch(error => console.error("Error:", error));
📄 Contoh Respon Berhasil (200 OK)
JSON
{
  "pesan": "Akses Diterima! Halo POS Kasir Cabang 1",
  "data": [
    {
      "product_name": "Beras Premium 5Kg",
      "category": "Sembako",
      "stock": 45,
      "final_price": 66500
    },
    {
      "product_name": "Minyak Goreng 2L",
      "category": "Sembako",
      "stock": 15,
      "final_price": 32000
    }
  ]
}
📄 Contoh Respon Gagal (401 Unauthorized)
(Terjadi jika API Key salah, kosong, atau aksesnya sudah di-Revoke oleh Admin)

JSON
{
  "pesan": "Akses Ditolak! API Key tidak valid atau sudah mati."
}
<p align="center"><i>Dikembangkan untuk operasional bisnis yang efisien dan akurat.</i></p>