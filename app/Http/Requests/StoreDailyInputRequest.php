<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDailyInputRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Tetap true, biarkan Controller/Middleware yang ngurusin role
        return true; 
    }

    // 🔥 TAMBAHAN TRICK SAKTI: Otomatis masukin user_id dari sesi login
    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user_id ?? Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'input_date' => ['required', 'date'],
            
            // 🔥 PERBAIKAN: Ganti 'integer' jadi 'numeric' biar aman buat desimal
            'hpp'        => ['required', 'numeric', 'min:0'],
            'c1'         => ['nullable', 'numeric', 'min:0'],
            'c2'         => ['nullable', 'numeric', 'min:0'],
            'c3'         => ['nullable', 'numeric', 'min:0'],
            
            'stock'      => ['required', 'integer', 'min:0'], // Stok tetap integer karena ga mungkin setengah barang
            
            'demand'     => ['required', 'in:tinggi,normal,rendah'],
            
            // 🔥 TAMBAHAN: Nangkep isian "Target Margin Manual" dari form lu
            'margin_override' => ['nullable', 'numeric', 'min:0', 'max:100'],
            
            'user_id'    => ['required', 'exists:users,id'],
        ];
    }
}