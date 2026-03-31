<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Pricing Engine Breyy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-[400px] md:max-w-[450px] lg:max-w-[500px] transition-all duration-500">
        
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
            
            <div class="bg-blue-800 p-6 text-center">
                <div class="inline-block bg-white p-3 rounded-full shadow-lg mb-3">
                    <i class="fas fa-calculator text-blue-600 text-2xl"></i>
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-white uppercase tracking-wider">Pricing Engine</h2>
                <p class="text-blue-100 text-xs md:text-sm mt-1">Sistem Manajemen Harga Internal</p>
            </div>

            <div class="p-6 md:p-8">
                <form action="/login" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Email Perusahaan</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                class="w-full pl-10 pr-4 py-3 border {{ $errors->has('email') ? 'border-red-500 ring-1 ring-red-500' : 'border-slate-300' }} rounded-xl outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm" 
                                placeholder="nama@perusahaan.com" required>
                        </div>
                        @error('email')
                            <div class="mt-2 text-[11px] md:text-xs text-red-600 flex items-center bg-red-50 p-2 rounded-lg border border-red-100">
                                <i class="fas fa-circle-exclamation mr-2"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" 
                                class="w-full pl-10 pr-4 py-3 border {{ $errors->has('email') ? 'border-red-500 ring-1 ring-red-500' : 'border-slate-300' }} rounded-xl outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm" 
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold transition-all duration-300 shadow-lg shadow-blue-200 active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>MASUK KE SISTEM</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>
            </div>

            <div class="bg-slate-50 p-4 border-t border-slate-100 text-center">
                <p class="text-[10px] md:text-xs text-slate-400 font-medium tracking-widest uppercase">
                    &copy; 2026 welcome to the pricing engine | Secure Access
                </p>
            </div>
        </div>
    </div>
</body>
</html>