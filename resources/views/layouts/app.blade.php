<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Regulator App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #475569; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 transition-colors duration-300 font-sans text-slate-800 dark:text-slate-200 flex h-screen overflow-hidden">

    @php
        // Ambil role user yang lagi login, default 'admin' kalau kosong
        $userRole = auth()->check() ? auth()->user()->role : 'admin';
    @endphp

    <div id="mobile-overlay" class="fixed inset-0 bg-slate-900/60 z-40 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <aside id="sidebar" class="fixed md:relative inset-y-0 left-0 z-50 w-64 bg-[#0f172a] flex flex-col transition-all duration-300 shadow-2xl md:shadow-xl shrink-0 -translate-x-full md:translate-x-0">
        
        <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="minw-8 w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                    <i class="fas fa-cube"></i>
                </div>
                <span class="text-white font-bold text-lg tracking-wide sidebar-text whitespace-nowrap">Pricing App</span>
            </div>
            <button id="toggle-sidebar" class="hidden md:block text-slate-400 hover:text-white transition-colors">
                <i class="fas fa-chevron-left" id="toggle-icon"></i>
            </button>
            <button id="close-mobile-sidebar" class="block md:hidden text-slate-400 hover:text-white transition-colors p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 space-y-8 custom-scrollbar">
            
      <div class="menu-group">
                <h3 class="px-5 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3 sidebar-text whitespace-nowrap">MAIN MENU</h3>
                <ul class="space-y-1 px-3">
                    
                    @if(in_array($userRole, ['administrator', 'manager']))
                    <li>
                        <a href="{{ route('dashboard') ?? '#' }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('dashboard') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-chart-line min-w-6 text-center {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                            <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('dashboard') ? 'font-bold' : 'font-medium' }}">Dashboard</span>
                        </a>
                    </li>
                    @endif

                    <li>
                        <a href="{{ route('daily-inputs.create') ?? '#' }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('daily-inputs.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-keyboard min-w-6 text-center {{ request()->routeIs('daily-inputs.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                            <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('daily-inputs.*') ? 'font-bold' : 'font-medium' }}">Input Harian</span>
                        </a>
                    </li>

                    @if($userRole === 'administrator')
                    <li>
                        <a href="{{ route('pricing.index') ?? '#' }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('pricing.index') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-calculator min-w-6 text-center {{ request()->routeIs('pricing.index') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                            <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('pricing.index') ? 'font-bold' : 'font-medium' }}">Pricing Engine</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array($userRole, ['administrator', 'manager']))
                 <li>
                        <a href="{{ route('pricing.history') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('pricing.history') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-history min-w-6 text-center {{ request()->routeIs('pricing.history') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                            <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('pricing.history') ? 'font-bold' : 'font-medium' }}">History</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('laporan.pricing') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('laporan.pricing') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-file-invoice-dollar min-w-6 text-center {{ request()->routeIs('laporan.pricing') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                            <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('laporan.pricing') ? 'font-bold' : 'font-medium' }}">Laporan Pricing</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('analytics.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('analytics.index') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-chart-pie min-w-6 text-center {{ request()->routeIs('analytics.index') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                            <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('analytics.index') ? 'font-bold' : 'font-medium' }}">Analytics</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

         <div class="menu-group">
                <h3 class="px-5 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3 sidebar-text whitespace-nowrap">SUPPLIER MANAGEMENT</h3>
                <ul class="space-y-1 px-3">
                   <li>
                        <a href="{{ route('suppliers.index') ?? '#' }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('suppliers.index', 'suppliers.create', 'suppliers.edit') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-truck min-w-6 text-center {{ request()->routeIs('suppliers.index', 'suppliers.create', 'suppliers.edit') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                            <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('suppliers.index', 'suppliers.create', 'suppliers.edit') ? 'font-bold' : 'font-medium' }}">Daftar Supplier</span>
                        </a>
                    </li>
                    
                    <li><a href="{{ route('quotations.create') ?? '#' }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('quotations.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-envelope-open-text min-w-6 text-center {{ request()->routeIs('quotations.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('quotations.*') ? 'font-bold' : 'font-medium' }}">Input Penawaran</span>
                    </a></li>
                    
                    @if(in_array($userRole, ['administrator', 'manager']))
                    <li><a href="{{ route('process_plan.index') ?? '#' }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('process_plan.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-clipboard-check min-w-6 text-center {{ request()->routeIs('process_plan.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('process_plan.*') ? 'font-bold' : 'font-medium' }}">Purchase Plan</span>
                    </a></li>
              <li><a href="{{ route('suppliers.products') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('suppliers.products') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-box-open min-w-6 text-center {{ request()->routeIs('suppliers.products') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('suppliers.products') ? 'font-bold' : 'font-medium' }}">Produk per Supplier</span>
                    </a></li>
                    @endif

                    @if(in_array($userRole, ['administrator', 'manager']))
                    <li><a href="{{ route('restock.index') ?? '#' }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('restock.*') ? 'text-white bg-indigo-600 shadow-lg shadow-indigo-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-warehouse min-w-6 text-center {{ request()->routeIs('restock.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-400' }}"></i>
                        <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('restock.*') ? 'font-bold' : 'font-medium' }}">Restock Gudang</span>
                    </a></li>
                    @endif
                </ul>
            </div>

            @if($userRole === 'administrator')
            <div class="menu-group">
                <h3 class="px-5 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3 sidebar-text whitespace-nowrap">PRODUCT & ENGINE</h3>
                <ul class="space-y-1 px-3">
                    
                    <li class="sidebar-nested">
                        <div class="flex items-center justify-between px-3 py-2.5 text-slate-300 rounded-xl transition-all hover:bg-slate-800 cursor-pointer group {{ request()->routeIs('products.*') ? 'bg-blue-900/20 border border-blue-800/50' : '' }}">
                            <div class="flex items-center">
                                <i class="fas fa-boxes min-w-6 text-center {{ request()->routeIs('products.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                                <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('products.*') ? 'text-white font-bold' : '' }}">Master Produk</span>
                            </div>
                            <i class="fas fa-chevron-down text-[10px] text-slate-500 sidebar-text"></i>
                        </div>
                        <ul class="pl-10 space-y-1 mt-1 sidebar-text hidden">
                           <li>
                                <a href="{{ route('products.index') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('products.index') && !request()->has('category') ? 'text-blue-400 font-bold' : 'text-slate-400 hover:text-white' }}">
                                    <i class="fas fa-list-ul mr-2"></i> Semua Produk (Unified)
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('products.index', ['category' => 'Produk Beli']) }}" class="block py-1.5 text-xs transition-colors {{ request('category') == 'Produk Beli' ? 'text-blue-400 font-bold' : 'text-slate-400 hover:text-white' }}">
                                    <i class="fas fa-shopping-cart mr-2"></i> Kategori: Produk Beli
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('products.index', ['category' => 'Produk Jual']) }}" class="block py-1.5 text-xs transition-colors {{ request('category') == 'Produk Jual' ? 'text-blue-400 font-bold' : 'text-slate-400 hover:text-white' }}">
                                    <i class="fas fa-store mr-2"></i> Kategori: Produk Jual
                                </a>
                            </li>
                        </ul>
                    </li>

                   <li class="sidebar-nested">
                        <div class="flex items-center justify-between px-3 py-2.5 text-slate-300 rounded-xl transition-all hover:bg-slate-800 cursor-pointer group {{ request()->routeIs('pricing.type') ? 'bg-blue-900/20 border border-blue-800/50' : '' }}">
                            <div class="flex items-center">
                                <i class="fas fa-tags min-w-6 text-center {{ request()->routeIs('pricing.type') ? 'text-blue-400' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                                <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('pricing.type') ? 'text-white font-bold' : 'font-medium' }}">Tipe Harga</span>
                            </div>
                            <i class="fas fa-chevron-down text-[10px] text-slate-500 sidebar-text"></i>
                        </div>
                        <ul class="pl-10 space-y-1 mt-1 sidebar-text {{ request()->routeIs('pricing.type') ? '' : 'hidden' }}">
                            <li><a href="{{ route('pricing.type', 'dynamic') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('pricing.type') && request()->route('type') == 'dynamic' ? 'text-blue-400 font-bold' : 'text-slate-400 hover:text-white' }}">Dynamic Pricing</a></li>
                            <li><a href="{{ route('pricing.type', 'consignment') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('pricing.type') && request()->route('type') == 'consignment' ? 'text-blue-400 font-bold' : 'text-slate-400 hover:text-white' }}">Consignment Product</a></li>
                            <li><a href="{{ route('pricing.type', 'HET') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('pricing.type') && request()->route('type') == 'HET' ? 'text-blue-400 font-bold' : 'text-slate-400 hover:text-white' }}">HET Product</a></li>
                        </ul>
                    </li>

                    <li><a href="{{ route('rules.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('rules.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-cogs min-w-6 text-center {{ request()->routeIs('rules.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('rules.*') ? 'font-bold' : 'font-medium' }}">Auto Adjustment Rules</span>
                    </a></li>
                    
                    <li><a href="{{ route('formula.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('formula.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-subscript min-w-6 text-center {{ request()->routeIs('formula.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('formula.*') ? 'font-bold' : 'font-medium' }}">Formula Settings</span>
                    </a></li>
                </ul>
            </div>
            @endif

        @if(in_array($userRole, ['administrator', 'manager']))
            <div class="menu-group">
                <h3 class="px-5 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3 sidebar-text whitespace-nowrap">DOCUMENT CENTER</h3>
                <ul class="space-y-1 px-3">
                    
                    <li><a href="{{ route('document.center', ['type' => 'all']) }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('document.center') && request()->route('type') == 'all' ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-folder min-w-6 text-center {{ request()->routeIs('document.center') && request()->route('type') == 'all' ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('document.center') && request()->route('type') == 'all' ? 'font-bold' : 'font-medium' }}">All Supplier Contracts</span>
                    </a></li>
                    
                    <li><a href="{{ route('contracts.upload') ?? '#' }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('contracts.upload') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-cloud-upload-alt min-w-6 text-center {{ request()->routeIs('contracts.upload') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('contracts.upload') ? 'font-bold' : 'font-medium' }}">Upload Kontrak</span>
                    </a></li>
                    
                    <li><a href="{{ route('document.center', ['type' => 'active']) }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('document.center') && request()->route('type') == 'active' ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-folder-open min-w-6 text-center {{ request()->routeIs('document.center') && request()->route('type') == 'active' ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('document.center') && request()->route('type') == 'active' ? 'font-bold' : 'font-medium' }}">Active Contracts</span>
                    </a></li>
                    
                    <li><a href="{{ route('document.center', ['type' => 'expired']) }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('document.center') && request()->route('type') == 'expired' ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-archive min-w-6 text-center {{ request()->routeIs('document.center') && request()->route('type') == 'expired' ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('document.center') && request()->route('type') == 'expired' ? 'font-bold' : 'font-medium' }}">Expired Contracts</span>
                    </a></li>
                    
                </ul>
            </div>
            @endif

            @if($userRole === 'administrator')
            <div class="menu-group">
                <h3 class="px-5 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3 sidebar-text whitespace-nowrap">ADMINISTRATION</h3>
                <ul class="space-y-1 px-3">
                  <li><a href="{{ route('users.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('users.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-users-cog min-w-6 text-center {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('users.*') ? 'font-bold' : 'font-medium' }}">User & Role Mgt</span>
                    </a></li>
                    
                    <li><a href="{{ route('audit_trail.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('audit_trail.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-shoe-prints min-w-6 text-center {{ request()->routeIs('audit_trail.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('audit_trail.*') ? 'font-bold' : 'font-medium' }}">Audit Trail</span>
                    </a></li>
                    
                 @php
                        // Hack kilat: Ngitung notifikasi yang belum dibaca dari tabel
                        $unreadCount = \App\Models\NotificationSystem::where('is_read', false)->count() ?? 0;
                    @endphp
                    
                    <li><a href="{{ route('notifications.index') }}" class="relative flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('notifications.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-bell min-w-6 text-center {{ request()->routeIs('notifications.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                            <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('notifications.*') ? 'font-bold' : 'font-medium' }}">Notification Center</span>
                        </div>
                        
                        @if($unreadCount > 0)
                            <span class="bg-cyan-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm sidebar-badge">{{ $unreadCount }}</span>
                        @endif
                    </a></li>
                    
                    <li><a href="{{ route('api.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('api.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-network-wired min-w-6 text-center {{ request()->routeIs('api.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm font-medium sidebar-text whitespace-nowrap {{ request()->routeIs('api.*') ? 'font-bold' : 'font-medium' }}">API Integration</span>
                    </a></li>
                    
                 <li><a href="{{ route('backup.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('backup.*') ? 'text-white bg-blue-600 shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-database min-w-6 text-center {{ request()->routeIs('backup.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap {{ request()->routeIs('backup.*') ? 'font-bold' : 'font-medium' }}">Backup & Restore</span>
                    </a></li>
                </ul>
            </div>
            @endif
        </nav>

        <div class="h-16 flex items-center justify-center px-4 border-t border-slate-800 shrink-0">
            <button id="theme-toggle" class="w-full flex items-center justify-center gap-3 py-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition-colors">
                <i class="fas fa-moon" id="theme-icon"></i>
                <span class="text-sm font-medium sidebar-text whitespace-nowrap" id="theme-text">Dark Mode</span>
            </button>
        </div>
    </aside>

    <div class="flex-1 w-full flex flex-col h-full overflow-hidden transition-all duration-300 relative z-20">
        
        <header class="h-16 bg-white dark:bg-[#1e293b] border-b border-slate-200 dark:border-slate-800 flex items-center justify-between md:justify-end px-4 md:px-6 shrink-0 z-20 transition-colors duration-300 shadow-sm relative">
            
            <button id="open-mobile-sidebar" class="md:hidden text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white p-2 focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>

            <div class="flex-1 md:hidden"></div>

            @php
                $name = auth()->check() ? auth()->user()->name : 'John Wilson';
                $email = auth()->check() ? auth()->user()->email : 'Wilson@gmail.com';
                $words = explode(' ', $name);
                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            @endphp

            <div class="relative">
                <button onclick="toggleUserDropdown()" class="flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-800 py-1 px-2 rounded-xl transition-colors focus:outline-none">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-none">{{ $name }}</p>
                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-1">{{ $userRole }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400 flex items-center justify-center font-bold text-sm">
                        {{ $initials }}
                    </div>
                </button>

                <div id="user-dropdown" style="z-index: 9999;" class="hidden absolute right-0 mt-3 w-64 bg-white dark:bg-[#1e293b] rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 overflow-hidden">
                    <div class="px-5 py-4 flex items-center gap-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400 flex items-center justify-center font-bold text-lg shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-base font-bold text-slate-800 dark:text-white truncate">{{ $name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $email }}</p>
                        </div>
                    </div>
                    
                    <div class="py-2">
                        <button type="button" onclick="openModalPassword()" class="w-full text-left flex items-center px-5 py-3 text-[14px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <i class="fas fa-key text-slate-400 w-6"></i> Ganti Password
                        </button>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                         <form action="{{ route('logout') ?? '#' }}" method="POST">
                             @csrf
                             <button type="submit" class="w-full text-left px-5 py-3 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 font-bold transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>  Logout
                            </button>
                         </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 bg-slate-50 dark:bg-slate-900 transition-colors duration-300 pb-24 md:pb-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 rounded-r-xl shadow-sm flex items-center">
                    <i class="fas fa-check-circle text-emerald-500 mr-3"></i>
                    <p class="font-bold text-emerald-800 dark:text-emerald-300 text-sm">{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-xl shadow-sm flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="font-bold text-red-800 dark:text-red-300 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-xl shadow-sm">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-times-circle text-red-500 mr-3"></i>
                        <p class="font-bold text-red-800 dark:text-red-300 text-sm">Ada kesalahan input:</p>
                    </div>
                    <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-400 ml-7">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <div id="modalGantiPassword" style="z-index: 9999;" class="fixed inset-0 items-center justify-center bg-black bg-opacity-70 hidden backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all border border-slate-100 dark:border-slate-700">
            
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-indigo-50 dark:bg-indigo-900/20">
                <h3 class="font-bold text-lg text-indigo-800 dark:text-indigo-300">
                    <i class="fas fa-key mr-2"></i> Ganti Password
                </h3>
                <button type="button" onclick="closeModalPassword()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('password.change') ?? '#' }}" method="POST" class="p-6">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password Lama</label>
                    <input type="password" name="old_password" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-white transition-all">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
                    <input type="password" name="new_password" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-white transition-all">
                    <p class="text-xs text-slate-500 mt-1.5"><i class="fas fa-info-circle"></i> Minimal 6 karakter</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-white transition-all">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModalPassword()" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl font-bold transition-all text-sm">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-600/30 transition-all text-sm flex items-center">
                        <i class="fas fa-save mr-2"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Logika Modal
        function openModalPassword() {
            const modal = document.getElementById('modalGantiPassword');
            modal.classList.remove('hidden');
            modal.classList.add('flex'); 
            document.getElementById('user-dropdown').classList.add('hidden'); 
        }

        function closeModalPassword() {
            const modal = document.getElementById('modalGantiPassword');
            modal.classList.add('hidden');
            modal.classList.remove('flex'); 
        }

        // Logika Dropdown Nested Sidebar
        const nestedItems = document.querySelectorAll('.sidebar-nested > div');
        nestedItems.forEach(item => {
            item.addEventListener('click', function() {
                const ul = this.nextElementSibling;
                ul.classList.toggle('hidden');
            });
        });

        // 1. Logika Collapse Sidebar (Desktop) & Drawer (Mobile)
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        const toggleBtnDesktop = document.getElementById('toggle-sidebar');
        const toggleIcon = document.getElementById('toggle-icon');
        const texts = document.querySelectorAll('.sidebar-text');
        const badges = document.querySelectorAll('.sidebar-badge');
        const openMobileBtn = document.getElementById('open-mobile-sidebar');
        const closeMobileBtn = document.getElementById('close-mobile-sidebar');
        
        let isCollapsedDesktop = localStorage.getItem('sidebarCollapsed') === 'true';

        function applySidebarState(isInstant = false) {
            if (window.innerWidth >= 768) {
                if (isCollapsedDesktop) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    toggleIcon.classList.replace('fa-chevron-left', 'fa-chevron-right');
                    texts.forEach(el => el.style.display = 'none');
                    badges.forEach(el => {
                        el.classList.add('absolute', 'top-1', 'right-2');
                        el.classList.remove('ml-auto');
                    });
                } else {
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    toggleIcon.classList.replace('fa-chevron-right', 'fa-chevron-left');
                    if (isInstant) {
                        texts.forEach(el => el.style.display = '');
                    } else {
                        setTimeout(() => { texts.forEach(el => el.style.display = ''); }, 150);
                    }
                    badges.forEach(el => {
                        el.classList.remove('absolute', 'top-1', 'right-2');
                        el.classList.add('ml-auto');
                    });
                }
            }
        }

        applySidebarState(true);

        openMobileBtn.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');
            }, 10);
        });

        function closeMobileSidebar() {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }

        closeMobileBtn.addEventListener('click', closeMobileSidebar);
        overlay.addEventListener('click', closeMobileSidebar);

        toggleBtnDesktop.addEventListener('click', () => {
            if(window.innerWidth >= 768) {
                isCollapsedDesktop = !isCollapsedDesktop;
                localStorage.setItem('sidebarCollapsed', isCollapsedDesktop);
                applySidebarState(false);
            }
        });

        // 2. Logika Dark Mode
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const themeText = document.getElementById('theme-text');
        const html = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
            themeText.innerText = 'Light Mode';
        }

        themeToggleBtn.addEventListener('click', () => {
            html.classList.toggle('dark');
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
                themeText.innerText = 'Light Mode';
            } else {
                localStorage.setItem('theme', 'light');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
                themeText.innerText = 'Dark Mode';
            }
        });

        // 3. Logika Dropdown User Profile
        function toggleUserDropdown() {
            const dropdown = document.getElementById('user-dropdown');
            dropdown.classList.toggle('hidden');
        }

        window.onclick = function(event) {
            if (!event.target.closest('.relative')) {
                const dropdowns = document.querySelectorAll("#user-dropdown:not(.hidden)");
                dropdowns.forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        }
    </script>
</body>
</html>