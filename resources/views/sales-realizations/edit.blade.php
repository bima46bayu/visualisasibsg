@extends('layouts.app')
@section('title', 'Edit Realisasi')
@section('header', 'Edit Sales Realisasi')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('sales-realizations.update', $sales_realization) }}" method="POST">
        @csrf @method('PUT')
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <input type="number" name="year" value="{{ old('year', $sales_realization->year) }}" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('year') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <input type="number" name="month" min="1" max="12" value="{{ old('month', $sales_realization->month) }}" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('month') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sales Member (AM)</label>
                <select name="sales_member_id" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Sales Member</option>
                    @foreach($sales_members as $member)
                        <option value="{{ $member->id }}" {{ old('sales_member_id', $sales_realization->sales_member_id) == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                    @endforeach
                </select>
                @error('sales_member_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Entity</label>
                <select name="entity_id" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Entity</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}" {{ old('entity_id', $sales_realization->entity_id) == $entity->id ? 'selected' : '' }}>{{ $entity->name }}</option>
                    @endforeach
                </select>
                @error('entity_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Realisasi Amount (Rp)</label>
                <input type="number" name="realization_amount" min="0" value="{{ old('realization_amount', round($sales_realization->realization_amount)) }}" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                @error('realization_amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 flex space-x-3">
            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">Update</button>
            <a href="{{ route('sales-realizations.index') }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
