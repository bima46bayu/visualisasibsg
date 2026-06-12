@extends('layouts.app')
@section('title', 'Master End User')
@section('header', 'Master End User')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 xl:p-6" x-data="{ 
    isAddOpen: false, 
    isEditOpen: false, 
    editData: {},
    openEdit(data) { this.editData = data; this.isEditOpen = true; }
}">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-slate-100 pb-5">
        <div>
            <a href="{{ route('sales-management.index') }}" class="text-xs text-slate-400 hover:text-blue-600 font-semibold flex items-center w-max mb-2 transition uppercase tracking-wider">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Manajemen Sales
            </a>
            <h2 class="text-xl xl:text-2xl font-bold text-slate-800 flex items-center">
                <span class="bg-amber-100 text-amber-600 p-2 rounded-lg mr-3 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
                Master End User
            </h2>
        </div>
        <div class="flex space-x-3 w-full md:w-auto">
            <form action="{{ route('end-users.index') }}" method="GET" class="flex flex-1 md:w-64 relative group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..." class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50 focus:bg-white placeholder-slate-400">
            </form>
            <button @click="isAddOpen = true" class="bg-amber-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-amber-600 hover:-translate-y-0.5 hover:shadow-md transition-all whitespace-nowrap flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                End User
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="px-4 py-3 font-bold whitespace-nowrap text-xs">Name</th>
                    <th class="px-4 py-3 font-bold whitespace-nowrap text-xs text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($endUsers as $endUser)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-2.5 whitespace-nowrap text-xs font-medium text-gray-900">{{ $endUser->name }}</td>
                    <td class="px-4 py-2.5 whitespace-nowrap text-xs text-right space-x-2">
                        <button @click="openEdit({ id: {{ $endUser->id }}, name: '{{ addslashes($endUser->name) }}' })" class="text-blue-600 hover:text-blue-800 font-medium mx-1">Edit</button>
                        <form id="delete-end-user-{{ $endUser->id }}" action="{{ route('end-users.destroy', $endUser) }}" method="POST" class="inline-block">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-end-user-{{ $endUser->id }}', 'Yakin hapus end user ini?')" class="text-red-600 hover:text-red-800 font-medium mx-1">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-4 py-2.5 whitespace-nowrap text-xs text-center text-gray-500">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $endUsers->links() }}
    </div>

    <!-- Add Modal -->
    <div x-show="isAddOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md" @click.away="isAddOpen = false">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah End User</h3>
            <form action="{{ route('end-users.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="isAddOpen = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="isEditOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md" @click.away="isEditOpen = false">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Edit End User</h3>
            <form :action="`/master/end-users/${editData.id}`" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" x-model="editData.name" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="isEditOpen = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
