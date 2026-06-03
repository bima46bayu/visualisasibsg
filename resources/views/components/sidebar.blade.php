<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="absolute inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col">
    <div class="flex items-center h-16 px-6 font-extrabold text-xl tracking-tight text-blue-600">
        VISUALIZEN
    </div>
    <div class="flex-1 overflow-y-auto py-4">
        <nav class="space-y-1 px-3">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 mt-4 px-4">Analytics</div>
            
            <!-- Menu Sales -->
            <div x-data="{ salesOpen: {{ request()->routeIs('dashboard') || request()->routeIs('sales-management.*') ? 'true' : 'false' }} }" class="mt-2">
                <button @click="salesOpen = !salesOpen" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('sales-management.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Sales
                    </div>
                    <svg :class="salesOpen ? 'transform rotate-180' : ''" class="w-4 h-4 transition-transform opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="salesOpen" class="bg-slate-50 rounded-b-lg -mt-1 pt-2 pb-1 border-x border-b border-slate-100">
                    <a href="{{ route('dashboard') }}" class="block px-12 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-slate-800' : 'text-slate-500 hover:text-blue-600' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('sales-management.index') }}" class="block px-12 py-2 text-sm font-medium transition-colors {{ request()->routeIs('sales-management.*') ? 'text-slate-800' : 'text-slate-500 hover:text-blue-600' }}">
                        Manajemen Sales
                    </a>
                </div>
            </div>
            
            <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors mt-2">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Settings
            </a>
        </nav>
    </div>
</aside>
