@extends('layouts.app')
@section('title', 'Master Sales Member')
@section('header', 'Master Sales Member')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-4">
        <form action="{{ route('sales-members.index') }}" method="GET" class="flex space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or code..." class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Search</button>
        </form>
        <a href="{{ route('sales-members.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700">+ Tambah Sales Member</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-600 p-4 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                    <th class="px-6 py-3 font-semibold">Code</th>
                    <th class="px-6 py-3 font-semibold">Name</th>
                    <th class="px-6 py-3 font-semibold">Team</th>
                    <th class="px-6 py-3 font-semibold">Status</th>
                    <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($sales_members as $member)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $member->code }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $member->name }}</td>
                    <td class="px-6 py-4">{{ $member->team ? $member->team->name : '-' }}</td>
                    <td class="px-6 py-4">
                        @if($member->status)
                            <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-semibold">Active</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('sales-members.edit', $member) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form action="{{ route('sales-members.destroy', $member) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus sales member ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $sales_members->links() }}
    </div>
</div>
@endsection
