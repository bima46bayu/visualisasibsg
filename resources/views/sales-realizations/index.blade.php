@extends('layouts.app')
@section('title', 'Sales Realisasi')
@section('header', 'Sales Realisasi')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <form action="{{ route('sales-realizations.index') }}" method="GET" class="flex space-x-2">
            <select name="year" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Tahun</option>
                @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="month" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Filter</button>
        </form>
        
        <div class="flex space-x-2">
            <div x-data="{ modalOpen: false }">
                <button @click="modalOpen = true" class="bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-amber-600">Import Excel</button>
                
                <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                    <div class="bg-white rounded-xl p-6 max-w-md w-full" @click.away="modalOpen = false">
                        <h3 class="text-lg font-bold mb-4">Import Realisasi</h3>
                        <form action="{{ route('sales-realizations.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg mb-4">
                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('sales-realizations.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700">+ Tambah</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-600 p-4 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-4 text-sm">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="px-6 py-3 font-semibold">Tahun</th>
                    <th class="px-6 py-3 font-semibold">Bulan</th>
                    <th class="px-6 py-3 font-semibold">Sales Member</th>
                    <th class="px-6 py-3 font-semibold">Entity</th>
                    <th class="px-6 py-3 font-semibold">Realisasi Amount</th>
                    <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($sales_realizations as $realization)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $realization->year }}</td>
                    <td class="px-6 py-4">{{ $realization->month }}</td>
                    <td class="px-6 py-4">{{ $realization->salesMember->name }}</td>
                    <td class="px-6 py-4">{{ $realization->entity->name }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-900">Rp {{ number_format($realization->realization_amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('sales-realizations.edit', $realization) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form action="{{ route('sales-realizations.destroy', $realization) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus realisasi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $sales_realizations->links() }}
    </div>
</div>
@endsection
