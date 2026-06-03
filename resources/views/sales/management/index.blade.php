@extends('layouts.app')
@section('title', 'Kelola Data Sales')

@section('content')
<div class="space-y-6 max-w-[1600px] mx-auto pb-10" x-data="{ tab: new URLSearchParams(location.search).get('realisasi_page') ? 'realisasi' : 'target' }">

    <!-- Header & Master Data Shortcuts -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Pusat Kelola Data</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('teams.index') }}" class="flex items-center p-4 border border-blue-100 bg-[#f8faff] rounded-lg hover:bg-blue-50 transition shadow-sm">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <div class="font-bold text-slate-800">Master Team</div>
                    <div class="text-xs text-slate-500">Kelola daftar tim divisi</div>
                </div>
            </a>
            
            <a href="{{ route('sales-members.index') }}" class="flex items-center p-4 border border-emerald-100 bg-[#f0fcf9] rounded-lg hover:bg-emerald-50 transition shadow-sm">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <div class="font-bold text-slate-800">Master Sales Member</div>
                    <div class="text-xs text-slate-500">Kelola akun dan target sales</div>
                </div>
            </a>

            <a href="{{ route('entities.index') }}" class="flex items-center p-4 border border-amber-100 bg-[#fffbeb] rounded-lg hover:bg-amber-50 transition shadow-sm">
                <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <div class="font-bold text-slate-800">Master Entity</div>
                    <div class="text-xs text-slate-500">Kelola entitas bisnis/produk</div>
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
                        <button onclick="document.getElementById('importTargetModal').classList.remove('hidden')" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import Excel
                        </button>
                        <a href="{{ route('sales-targets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center">
                            + Tambah Target
                        </a>
                    </div>
                </div>

                <!-- Filter Target -->
                <form action="{{ route('sales-management.index') }}" method="GET" class="flex flex-wrap gap-3 mb-6 bg-slate-50 p-4 rounded-lg border border-gray-100">
                    <input type="hidden" name="tab" value="target">
                    <select name="target_year" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ request('target_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="target_month" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('target_month') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-900 transition">Filter Target</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 border border-gray-200 rounded-lg">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 font-bold">Periode</th>
                                <th class="px-6 py-3 font-bold">Sales Member</th>
                                <th class="px-6 py-3 font-bold">Entity</th>
                                <th class="px-6 py-3 font-bold">Target Amount</th>
                                <th class="px-6 py-3 font-bold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales_targets as $target)
                            <tr class="bg-white border-b border-gray-50 hover:bg-slate-50 transition">
                                <td class="px-6 py-4">{{ $target->month }}/{{ $target->year }}</td>
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $target->salesMember->name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $target->entity->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-emerald-600 font-semibold">Rp {{ number_format($target->target_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('sales-targets.edit', $target->id) }}" class="text-blue-600 hover:text-blue-900 mx-1">Edit</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data target.</td>
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
                        <button onclick="document.getElementById('importRealisasiModal').classList.remove('hidden')" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import Excel
                        </button>
                        <a href="{{ route('sales-realizations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center">
                            + Tambah Realisasi
                        </a>
                    </div>
                </div>

                <!-- Filter Realisasi -->
                <form action="{{ route('sales-management.index') }}" method="GET" class="flex flex-wrap gap-3 mb-6 bg-slate-50 p-4 rounded-lg border border-gray-100">
                    <input type="hidden" name="tab" value="realisasi">
                    <select name="realisasi_year" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ request('realisasi_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="realisasi_month" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('realisasi_month') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-900 transition">Filter Realisasi</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 border border-gray-200 rounded-lg">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 font-bold">Periode</th>
                                <th class="px-6 py-3 font-bold">Sales Member</th>
                                <th class="px-6 py-3 font-bold">Entity</th>
                                <th class="px-6 py-3 font-bold">Realisasi Amount</th>
                                <th class="px-6 py-3 font-bold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales_realizations as $realization)
                            <tr class="bg-white border-b border-gray-50 hover:bg-slate-50 transition">
                                <td class="px-6 py-4">{{ $realization->month }}/{{ $realization->year }}</td>
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $realization->salesMember->name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $realization->entity->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-emerald-600 font-semibold">Rp {{ number_format($realization->realization_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('sales-realizations.edit', $realization->id) }}" class="text-blue-600 hover:text-blue-900 mx-1">Edit</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data realisasi.</td>
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
</div>

<!-- Import Modals -->
<!-- Target Modal -->
<div id="importTargetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Import Data Target</h3>
        <form action="{{ route('sales-targets.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Excel (.xlsx, .xls)</label>
                <input type="file" name="file" class="w-full border-gray-300 rounded-lg" required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('importTargetModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Realisasi Modal -->
<div id="importRealisasiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Import Data Realisasi</h3>
        <form action="{{ route('sales-realizations.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Excel (.xlsx, .xls)</label>
                <input type="file" name="file" class="w-full border-gray-300 rounded-lg" required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('importRealisasiModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Import</button>
            </div>
        </form>
    </div>
</div>
@endsection
