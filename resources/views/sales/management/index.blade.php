@extends('layouts.app')
@section('title', 'Kelola Data Sales')

@section('content')
<div class="space-y-6 max-w-[1600px] mx-auto pb-10" x-data="{ 
    tab: new URLSearchParams(location.search).get('realisasi_page') || new URLSearchParams(location.search).get('tab') === 'realisasi' ? 'realisasi' : 'target',
    
    // Target
    isAddTargetOpen: false,
    isEditTargetOpen: false,
    editTargetData: {},
    openEditTarget(data) { this.editTargetData = data; this.isEditTargetOpen = true; },
    
    // Realisasi
    isAddRealisasiOpen: false,
    isEditRealisasiOpen: false,
    editRealisasiData: {},
    openEditRealisasi(data) { this.editRealisasiData = data; this.isEditRealisasiOpen = true; }
}">

    <!-- Header & Master Data Shortcuts -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 xl:p-6 mb-6">
        <h2 class="text-lg xl:text-xl font-bold text-slate-800 mb-4">Pusat Kelola Data</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 xl:gap-4">
            <a href="{{ route('teams.index') }}" class="flex items-center p-3 xl:p-4 border border-blue-100 bg-gradient-to-br from-white to-blue-50 rounded-xl hover:-translate-y-1 transition-all duration-300 shadow-sm hover:shadow-md group">
                <div class="w-10 h-10 xl:w-12 xl:h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mr-3 xl:mr-4 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 xl:w-6 xl:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <div class="font-bold text-sm xl:text-base text-slate-800">Master Team</div>
                    <div class="text-[10px] xl:text-xs text-slate-500 line-clamp-1">Kelola daftar tim divisi</div>
                </div>
            </a>
            
            <a href="{{ route('sales-members.index') }}" class="flex items-center p-3 xl:p-4 border border-emerald-100 bg-gradient-to-br from-white to-emerald-50 rounded-xl hover:-translate-y-1 transition-all duration-300 shadow-sm hover:shadow-md group">
                <div class="w-10 h-10 xl:w-12 xl:h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 xl:mr-4 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 xl:w-6 xl:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <div class="font-bold text-sm xl:text-base text-slate-800">Master Sales Member</div>
                    <div class="text-[10px] xl:text-xs text-slate-500 line-clamp-1">Kelola akun dan target sales</div>
                </div>
            </a>

            <a href="{{ route('entities.index') }}" class="flex items-center p-3 xl:p-4 border border-amber-100 bg-gradient-to-br from-white to-amber-50 rounded-xl hover:-translate-y-1 transition-all duration-300 shadow-sm hover:shadow-md group">
                <div class="w-10 h-10 xl:w-12 xl:h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mr-3 xl:mr-4 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 xl:w-6 xl:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <div class="font-bold text-sm xl:text-base text-slate-800">Master Entity</div>
                    <div class="text-[10px] xl:text-xs text-slate-500 line-clamp-1">Kelola entitas bisnis/produk</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Wizard Navigation (Tabs) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex border-b border-gray-200 bg-gray-50">
            <button @click="tab = 'target'" :class="{'bg-white border-t-2 border-blue-600 text-blue-700': tab === 'target', 'text-gray-500 hover:bg-gray-100': tab !== 'target'}" class="flex-1 py-4 px-6 text-sm font-bold text-center transition-colors">
                1. Data Target Sales
            </button>
            <button @click="tab = 'realisasi'" :class="{'bg-white border-t-2 border-blue-600 text-blue-700': tab === 'realisasi', 'text-gray-500 hover:bg-gray-100': tab !== 'realisasi'}" class="flex-1 py-4 px-6 text-sm font-bold text-center transition-colors">
                2. Data Realisasi Sales
            </button>
        </div>

        <div class="p-6">
            <!-- TAB 1: TARGET -->
            <div x-show="tab === 'target'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Kelola Target</h3>
                    <div class="flex gap-2">
                        <button onclick="document.getElementById('importTargetModal').classList.remove('hidden')" class="bg-emerald-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-emerald-700 transition flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import Excel
                        </button>
                        <button @click="isAddTargetOpen = true" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition flex items-center">
                            + Tambah Target
                        </button>
                    </div>
                </div>

                <!-- Filter Target -->
                <form action="{{ route('sales-management.index') }}" method="GET" class="flex flex-wrap gap-3 mb-6 bg-slate-50 p-4 rounded-lg border border-gray-100">
                    <input type="hidden" name="tab" value="target">
                    <select name="target_year" class="border-gray-200 py-1.5 px-3 rounded-md text-xs focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ request('target_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="target_month" class="border-gray-200 py-1.5 px-3 rounded-md text-xs focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('target_month') == $m ? 'selected' : '' }}>{{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$m - 1] }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="bg-slate-800 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-slate-900 transition">Filter Target</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 border border-gray-200 rounded-lg">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs">Periode</th>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs">Sales Member</th>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs">Entity</th>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs">Target Amount</th>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales_targets as $target)
                            <tr class="bg-white border-b border-gray-50 hover:bg-slate-50 transition">
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs">{{ $target->month }}/{{ $target->year }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs font-medium text-slate-800">{{ $target->salesMember->name ?? '-' }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs">{{ $target->entity->name ?? '-' }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs text-emerald-600 font-semibold">Rp {{ number_format($target->target_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs text-center">
                                    <button @click="openEditTarget({ id: {{ $target->id }}, year: {{ $target->year }}, month: {{ $target->month }}, sales_member_name: '{{ $target->salesMember->name ?? '' }}', entity_name: '{{ $target->entity->name ?? '' }}', target_amount: {{ $target->target_amount }} })" class="text-blue-600 hover:text-blue-900 mx-1">Edit</button>
                                    <form id="delete-target-{{ $target->id }}" action="{{ route('sales-targets.destroy', $target->id) }}" method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-target-{{ $target->id }}', 'Yakin hapus target ini?')" class="text-red-600 hover:text-red-900 mx-1">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-2.5 whitespace-nowrap text-xs text-center text-gray-500">Belum ada data target.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $sales_targets->appends(request()->except('target_page'))->links() }}
                </div>
            </div>

            <!-- TAB 2: REALISASI -->
            <div x-show="tab === 'realisasi'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Kelola Realisasi</h3>
                    <div class="flex gap-2">
                        <button onclick="document.getElementById('importRealisasiModal').classList.remove('hidden')" class="bg-emerald-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-emerald-700 transition flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import Excel
                        </button>
                        <button @click="isAddRealisasiOpen = true" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition flex items-center">
                            + Tambah Realisasi
                        </button>
                    </div>
                </div>

                <!-- Filter Realisasi -->
                <form action="{{ route('sales-management.index') }}" method="GET" class="flex flex-wrap gap-3 mb-6 bg-slate-50 p-4 rounded-lg border border-gray-100">
                    <input type="hidden" name="tab" value="realisasi">
                    <select name="realisasi_year" class="border-gray-200 py-1.5 px-3 rounded-md text-xs focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ request('realisasi_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="realisasi_month" class="border-gray-200 py-1.5 px-3 rounded-md text-xs focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('realisasi_month') == $m ? 'selected' : '' }}>{{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$m - 1] }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="bg-slate-800 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-slate-900 transition">Filter Realisasi</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 border border-gray-200 rounded-lg">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs">Periode</th>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs">Sales Member</th>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs">Entity</th>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs">Realisasi Amount</th>
                                <th class="px-4 py-3 font-bold whitespace-nowrap text-xs text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales_realizations as $realization)
                            <tr class="bg-white border-b border-gray-50 hover:bg-slate-50 transition">
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs">{{ $realization->month }}/{{ $realization->year }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs font-medium text-slate-800">{{ $realization->salesMember->name ?? '-' }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs">{{ $realization->entity->name ?? '-' }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs text-emerald-600 font-semibold">Rp {{ number_format($realization->realization_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs text-center">
                                    <button @click="openEditRealisasi({ id: {{ $realization->id }}, year: {{ $realization->year }}, month: {{ $realization->month }}, sales_member_name: '{{ $realization->salesMember->name ?? '' }}', entity_name: '{{ $realization->entity->name ?? '' }}', realization_amount: {{ $realization->realization_amount }} })" class="text-blue-600 hover:text-blue-900 mx-1">Edit</button>
                                    <form id="delete-realisasi-{{ $realization->id }}" action="{{ route('sales-realizations.destroy', $realization->id) }}" method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-realisasi-{{ $realization->id }}', 'Yakin hapus realisasi ini?')" class="text-red-600 hover:text-red-900 mx-1">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-2.5 whitespace-nowrap text-xs text-center text-gray-500">Belum ada data realisasi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $sales_realizations->appends(request()->except('realisasi_page'))->links() }}
                </div>

            </div>
        </div>
    </div>

    <!-- TARGET MODALS -->
    <!-- Add Target Modal -->
    <div x-show="isAddTargetOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md" @click.away="isAddTargetOpen = false">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Target Sales</h3>
            <form action="{{ route('sales-targets.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select name="year" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition bg-white" required>
                            @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select name="month" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition bg-white" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$m - 1] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sales Member</label>
                    <input type="text" name="sales_member_name" list="am-list" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" placeholder="Pilih dari daftar atau ketik baru..." required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entity</label>
                    <input type="text" name="entity_name" list="entity-list" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" placeholder="Pilih dari daftar atau ketik baru..." required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Amount (Rp)</label>
                    <input type="number" name="target_amount" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="isAddTargetOpen = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Target Modal -->
    <div x-show="isEditTargetOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md" @click.away="isEditTargetOpen = false">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Edit Target Sales</h3>
            <form :action="`/sales/targets/${editTargetData.id}`" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select name="year" x-model="editTargetData.year" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition bg-white" required>
                            @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select name="month" x-model="editTargetData.month" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition bg-white" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$m - 1] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sales Member</label>
                    <input type="text" name="sales_member_name" list="am-list" x-model="editTargetData.sales_member_name" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entity</label>
                    <input type="text" name="entity_name" list="entity-list" x-model="editTargetData.entity_name" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Amount (Rp)</label>
                    <input type="number" name="target_amount" x-model="editTargetData.target_amount" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="isEditTargetOpen = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>


    <!-- REALISASI MODALS -->
    <!-- Add Realisasi Modal -->
    <div x-show="isAddRealisasiOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md" @click.away="isAddRealisasiOpen = false">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Realisasi Sales</h3>
            <form action="{{ route('sales-realizations.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select name="year" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition bg-white" required>
                            @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select name="month" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition bg-white" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$m - 1] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sales Member</label>
                    <input type="text" name="sales_member_name" list="am-list" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" placeholder="Pilih dari daftar atau ketik baru..." required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entity</label>
                    <input type="text" name="entity_name" list="entity-list" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" placeholder="Pilih dari daftar atau ketik baru..." required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Realisasi Amount (Rp)</label>
                    <input type="number" name="realization_amount" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="isAddRealisasiOpen = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Realisasi Modal -->
    <div x-show="isEditRealisasiOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md" @click.away="isEditRealisasiOpen = false">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Edit Realisasi Sales</h3>
            <form :action="`/sales/realizations/${editRealisasiData.id}`" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select name="year" x-model="editRealisasiData.year" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition bg-white" required>
                            @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select name="month" x-model="editRealisasiData.month" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition bg-white" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$m - 1] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sales Member</label>
                    <input type="text" name="sales_member_name" list="am-list" x-model="editRealisasiData.sales_member_name" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entity</label>
                    <input type="text" name="entity_name" list="entity-list" x-model="editRealisasiData.entity_name" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Realisasi Amount (Rp)</label>
                    <input type="number" name="realization_amount" x-model="editRealisasiData.realization_amount" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="isEditRealisasiOpen = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>


</div>

<!-- Import Modals -->
<!-- Target Modal -->
<div id="importTargetModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Import Data Target</h3>
        <form action="{{ route('sales-targets.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Excel (.xlsx, .xls)</label>
                <input type="file" name="file" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                <p class="text-xs text-slate-500 mt-2">
                    Belum punya formatnya? <a href="{{ asset('templates/template_target.xlsx') }}" download class="text-blue-600 font-medium hover:underline">Download Template</a>
                </p>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('importTargetModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Realisasi Modal -->
<div id="importRealisasiModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Import Data Realisasi</h3>
        <form action="{{ route('sales-realizations.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Excel (.xlsx, .xls)</label>
                <input type="file" name="file" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                <p class="text-xs text-slate-500 mt-2">
                    Belum punya formatnya? <a href="{{ asset('templates/template_realisasi.xlsx') }}" download class="text-blue-600 font-medium hover:underline">Download Template</a>
                </p>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('importRealisasiModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Import</button>
            </div>
        </form>
    </div>
</div>
<!-- Datalists for Auto-Complete -->
<datalist id="am-list">
    @foreach($sales_members as $sm)
        <option value="{{ $sm->name }}"></option>
    @endforeach
</datalist>
<datalist id="entity-list">
    @foreach($entities as $ent)
        <option value="{{ $ent->name }}"></option>
    @endforeach
</datalist>
<datalist id="year-list">
    @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
        <option value="{{ $y }}"></option>
    @endfor
</datalist>
<datalist id="month-list">
    @for($m = 1; $m <= 12; $m++)
        <option value="{{ $m }}">{{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$m - 1] }}</option>
    @endfor
</datalist>
@endsection
