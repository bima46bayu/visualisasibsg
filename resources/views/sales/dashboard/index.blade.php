@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6 max-w-[1600px] mx-auto pb-10 font-sans text-slate-800 mt-2">

    <div class="flex justify-between items-center mb-2">
        <h2 class="text-3xl font-bold text-slate-900">Dashboard</h2>
        <!-- <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm hover:bg-blue-700 transition-colors">+ New Report</button> -->
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col md:flex-row gap-4 items-center justify-between mb-8">
        <h3 class="text-lg font-semibold text-slate-800">My Sales Analytics</h3>
        <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <select name="year" class="border-slate-300 text-slate-700 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm py-2 px-3">
                <option value="">Semua Tahun</option>
                @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="month" class="border-slate-300 text-slate-700 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm py-2 px-3">
                <option value="">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                @endfor
            </select>
            <select name="team_id" class="border-slate-300 text-slate-700 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm py-2 px-3">
                <option value="">Semua Team</option>
                @foreach(\App\Models\Team::all() as $team)
                    <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                @endforeach
            </select>
            <select name="entity_id" class="border-slate-300 text-slate-700 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm py-2 px-3">
                <option value="">Semua Entity</option>
                @foreach(\App\Models\Entity::all() as $entity)
                    <option value="{{ $entity->id }}" {{ request('entity_id') == $entity->id ? 'selected' : '' }}>{{ $entity->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm">Terapkan</button>
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors ml-1">Reset</a>
        </form>
    </div>

    @php
        function getStatusColor($pct) {
            if ($pct >= 100) return 'text-emerald-600';
            if ($pct >= 80) return 'text-amber-500';
            if ($pct > 0) return 'text-rose-500';
            return 'text-slate-500';
        }
        function getDotColor($pct) {
            if ($pct >= 100) return 'bg-emerald-500 border-emerald-600';
            if ($pct >= 80) return 'bg-amber-300 border-amber-400';
            if ($pct > 0) return 'bg-rose-500 border-rose-600';
            return 'bg-slate-300 border-slate-400';
        }

        function formatAchPlain($target, $real) {
            $pct = $target > 0 ? ($real / $target) * 100 : 0;
            $color = getStatusColor($pct);
            $dot = getDotColor($pct);
            return '<div class="flex items-center justify-end gap-2 pr-2"><div class="w-3 h-3 rounded-full border ' . $dot . ' shadow-sm"></div><span class="font-semibold '.$color.' min-w-[3rem] text-right">'.number_format($pct, 1).'%</span></div>';
        }
    @endphp

    <!-- Section 1: Core Business Data -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        
        <!-- Card 1: Blue -->
        <div class="bg-blue-50/50 rounded-2xl border border-blue-100 p-5 flex flex-col justify-center h-full relative overflow-hidden">
            <div class="text-slate-500 font-medium text-sm mb-1 z-10">Total Realisasi (Rp)</div>
            <div class="text-3xl font-bold text-slate-900 mb-2 z-10">{{ number_format($data['total_realization'], 0, ',', '.') }}</div>
            <div class="text-xs font-medium text-slate-400 z-10">
                <span class="{{ getStatusColor($data['overall_achievement_percentage']) }}">Achievement: {{ number_format($data['overall_achievement_percentage'], 1) }}%</span>
            </div>
            <div class="absolute top-4 right-4 w-8 h-8 rounded border border-blue-200 flex items-center justify-center text-blue-500 bg-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Card 2: Purple -->
        <div class="bg-purple-50/50 rounded-2xl border border-purple-100 p-5 flex flex-col justify-center h-full relative overflow-hidden">
            <div class="text-slate-500 font-medium text-sm mb-1 z-10">Total Target (Rp)</div>
            <div class="text-3xl font-bold text-slate-900 mb-2 z-10">{{ number_format($data['total_target'], 0, ',', '.') }}</div>
            <div class="text-xs font-medium text-slate-400 z-10">
                Base Target Set
            </div>
            <div class="absolute top-4 right-4 w-8 h-8 rounded border border-purple-200 flex items-center justify-center text-purple-500 bg-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Card 3: Emerald -->
        <div class="bg-emerald-50/50 rounded-2xl border border-emerald-100 p-5 flex flex-col justify-center h-full relative overflow-hidden">
            <div class="text-slate-500 font-medium text-sm mb-1 z-10">Overall Achievement</div>
            <div class="text-3xl font-bold text-slate-900 mb-2 z-10">{{ number_format($data['overall_achievement_percentage'], 1) }}%</div>
            <div class="text-xs font-medium text-slate-400 z-10">
                Dari target setahun
            </div>
            <div class="absolute top-4 right-4 w-8 h-8 rounded border border-emerald-200 flex items-center justify-center text-emerald-500 bg-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>

        <!-- Card 4: Orange -->
        <div class="bg-orange-50/50 rounded-2xl border border-orange-100 p-5 flex flex-col justify-center h-full relative overflow-hidden">
            <div class="text-slate-500 font-medium text-sm mb-1 z-10">Top Entity</div>
            @php
                $topEnt = collect($data['achievement_per_entity'])->sortByDesc('realization')->first();
            @endphp
            <div class="text-2xl font-bold text-slate-900 mb-2 z-10">{{ $topEnt ? $topEnt['entity'] : '-' }}</div>
            <div class="text-xs font-medium text-slate-400 z-10">
                Penyumbang tertinggi
            </div>
            <div class="absolute top-4 right-4 w-8 h-8 rounded border border-orange-200 flex items-center justify-center text-orange-500 bg-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            </div>
        </div>
    </div>


    <!-- Monthly Trend Chart -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-base font-semibold text-slate-800">Target vs Realisasi (Monthly Trend)</h3>
        </div>
        <div class="h-[300px] w-full">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Section 2: Charts (Gabung dalam satu grid agar side-by-side) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        
        <!-- Left (span 2): Bar Chart -->
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
            <h3 class="text-base font-semibold text-slate-800 mb-4">Team Achievement</h3>
            <div class="h-[300px] w-full">
                <canvas id="teamChart"></canvas>
            </div>
        </div>

        <!-- Right (span 1): Donut Chart -->
        <div class="xl:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
            <h3 class="text-base font-semibold text-slate-800 mb-4">Entity Distribution</h3>
            <div class="h-[300px] w-full flex justify-center">
                <canvas id="entityChart"></canvas>
            </div>
        </div>
    </div>


    <!-- Top Performers List -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <div class="xl:col-span-3 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col h-full">
            <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h3 class="text-base font-semibold text-slate-800">Top 10 Sales</h3>
                <a href="{{ route('sales-members.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1 rounded-full">
                    View All
                </a>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[400px]">
                @forelse($data['top_sales'] as $index => $sales)
                    <div class="flex justify-between items-center border-b border-slate-50 pb-3 last:border-0 last:pb-0 px-2">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                #{{ $index + 1 }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">{{ $sales['name'] }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $sales['team'] }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold {!! getStatusColor($sales['achievement_percentage']) !!}">{{ number_format($sales['achievement_percentage'], 1) }}%</div>
                            <div class="text-xs text-slate-500 mt-0.5">Rp {{ number_format($sales['realization'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada data sales.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Entity Trend Line Charts -->
    <h3 class="text-lg font-semibold text-slate-800 mt-6 mb-2">Entity Performance Trends</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($data['entity_trends'] as $index => $trend)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <div class="text-sm font-semibold text-slate-700 mb-4 text-center">
                {{ $trend['entity'] }}
            </div>
            <div class="h-48 w-full">
                <canvas id="entityLineChart_{{ $index }}"></canvas>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pivot Tables -->
    @php
        $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        $currMonthName = $months[(int)$data['filter_meta']['curr_month']];
        $prevMonthName = $data['filter_meta']['prev_month'] ? $months[(int)$data['filter_meta']['prev_month']] : '-';
        $filterYear = $data['filter_meta']['year'];
    @endphp

    <h3 class="text-lg font-semibold text-slate-800 mt-8 mb-2">Sales Achievement Pivot</h3>

    <!-- Pivot Table 1: By AM -> Entity -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <th rowspan="2" class="border-r border-slate-200 px-4 py-3 text-left w-64 bg-slate-50 sticky left-0 z-10">By AM / Team / Entity</th>
                        <th colspan="3" class="border-r border-slate-200 px-3 py-2 text-center">{{ $prevMonthName }}</th>
                        <th colspan="3" class="border-r border-slate-200 px-3 py-2 text-center">{{ $currMonthName }}</th>
                        <th colspan="3" class="px-3 py-2 text-center">Total {{ $filterYear }}</th>
                    </tr>
                    <tr class="bg-slate-50 text-slate-600 font-medium border-b border-slate-200 text-xs">
                        <th class="border-r border-slate-200 px-3 py-2">Target</th>
                        <th class="border-r border-slate-200 px-3 py-2">Realisasi</th>
                        <th class="border-r border-slate-200 px-3 py-2">%</th>
                        <th class="border-r border-slate-200 px-3 py-2">Target</th>
                        <th class="border-r border-slate-200 px-3 py-2">Realisasi</th>
                        <th class="border-r border-slate-200 px-3 py-2">%</th>
                        <th class="border-r border-slate-200 px-3 py-2">Target</th>
                        <th class="border-r border-slate-200 px-3 py-2">Realisasi</th>
                        <th class="px-3 py-2">%</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    @foreach($data['pivot_table']['by_am'] as $am)
                    <tr class="bg-slate-100 font-semibold text-slate-800 border-b border-slate-200">
                        <td class="border-r border-slate-200 px-4 py-2 text-left sticky left-0 bg-slate-100 z-10">{{ $am['name'] }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($am['prev']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($am['prev']['realization']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2 text-center">{!! formatAchPlain($am['prev']['target'], $am['prev']['realization']) !!}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($am['curr']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($am['curr']['realization']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2 text-center">{!! formatAchPlain($am['curr']['target'], $am['curr']['realization']) !!}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($am['total']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($am['total']['realization']) }}</td>
                        <td class="px-3 py-2 text-center">{!! formatAchPlain($am['total']['target'], $am['total']['realization']) !!}</td>
                    </tr>
                    @foreach($am['entities'] as $ent)
                    <tr class="bg-white text-slate-600 border-b border-slate-100">
                        <td class="border-r border-slate-200 px-4 py-2 text-left pl-8 sticky left-0 bg-white z-10">{{ $ent['name'] }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['prev']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['prev']['realization']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2 text-center">{!! formatAchPlain($ent['prev']['target'], $ent['prev']['realization']) !!}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['curr']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['curr']['realization']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2 text-center">{!! formatAchPlain($ent['curr']['target'], $ent['curr']['realization']) !!}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['total']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['total']['realization']) }}</td>
                        <td class="px-3 py-2 text-center">{!! formatAchPlain($ent['total']['target'], $ent['total']['realization']) !!}</td>
                    </tr>
                    @endforeach
                    @endforeach
                    
                    <tr class="bg-blue-50 font-bold text-slate-800 border-t border-slate-300">
                        <td class="border-r border-slate-300 px-4 py-3 text-left sticky left-0 bg-blue-50 z-10">Subtotal</td>
                        @php 
                            $totalPrevT = collect($data['pivot_table']['by_am'])->sum('prev.target');
                            $totalPrevR = collect($data['pivot_table']['by_am'])->sum('prev.realization');
                            $totalCurrT = collect($data['pivot_table']['by_am'])->sum('curr.target');
                            $totalCurrR = collect($data['pivot_table']['by_am'])->sum('curr.realization');
                            $totalTotT = collect($data['pivot_table']['by_am'])->sum('total.target');
                            $totalTotR = collect($data['pivot_table']['by_am'])->sum('total.realization');
                        @endphp
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalPrevT) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalPrevR) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3 text-center">{!! formatAchPlain($totalPrevT, $totalPrevR) !!}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalCurrT) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalCurrR) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3 text-center">{!! formatAchPlain($totalCurrT, $totalCurrR) !!}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalTotT) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalTotR) }}</td>
                        <td class="px-3 py-3 text-center">{!! formatAchPlain($totalTotT, $totalTotR) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    <!-- Pivot Table 2: By Team -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <th rowspan="2" class="border-r border-slate-200 px-4 py-3 text-left w-64 bg-slate-50 sticky left-0 z-10">By Team</th>
                        <th colspan="3" class="border-r border-slate-200 px-3 py-2 text-center">{{ $prevMonthName }}</th>
                        <th colspan="3" class="border-r border-slate-200 px-3 py-2 text-center">{{ $currMonthName }}</th>
                        <th colspan="3" class="px-3 py-2 text-center">Total {{ $filterYear }}</th>
                    </tr>
                    <tr class="bg-slate-50 text-slate-600 font-medium border-b border-slate-200 text-xs">
                        <th class="border-r border-slate-200 px-3 py-2">Target</th>
                        <th class="border-r border-slate-200 px-3 py-2">Realisasi</th>
                        <th class="border-r border-slate-200 px-3 py-2">%</th>
                        <th class="border-r border-slate-200 px-3 py-2">Target</th>
                        <th class="border-r border-slate-200 px-3 py-2">Realisasi</th>
                        <th class="border-r border-slate-200 px-3 py-2">%</th>
                        <th class="border-r border-slate-200 px-3 py-2">Target</th>
                        <th class="border-r border-slate-200 px-3 py-2">Realisasi</th>
                        <th class="px-3 py-2">%</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    @foreach($data['pivot_table']['by_team'] as $team)
                    <tr class="bg-white text-slate-700 border-b border-slate-100">
                        <td class="border-r border-slate-200 px-4 py-2 text-left sticky left-0 bg-white z-10">{{ $team['name'] }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($team['prev']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($team['prev']['realization']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2 text-center">{!! formatAchPlain($team['prev']['target'], $team['prev']['realization']) !!}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($team['curr']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($team['curr']['realization']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2 text-center">{!! formatAchPlain($team['curr']['target'], $team['curr']['realization']) !!}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($team['total']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($team['total']['realization']) }}</td>
                        <td class="px-3 py-2 text-center">{!! formatAchPlain($team['total']['target'], $team['total']['realization']) !!}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-blue-50 font-bold text-slate-800 border-t border-slate-300">
                        <td class="border-r border-slate-300 px-4 py-3 text-left sticky left-0 bg-blue-50 z-10">Subtotal</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalPrevT) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalPrevR) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3 text-center">{!! formatAchPlain($totalPrevT, $totalPrevR) !!}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalCurrT) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalCurrR) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3 text-center">{!! formatAchPlain($totalCurrT, $totalCurrR) !!}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalTotT) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalTotR) }}</td>
                        <td class="px-3 py-3 text-center">{!! formatAchPlain($totalTotT, $totalTotR) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pivot Table 3: By Entity -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <th rowspan="2" class="border-r border-slate-200 px-4 py-3 text-left w-64 bg-slate-50 sticky left-0 z-10">By Entity</th>
                        <th colspan="3" class="border-r border-slate-200 px-3 py-2 text-center">{{ $prevMonthName }}</th>
                        <th colspan="3" class="border-r border-slate-200 px-3 py-2 text-center">{{ $currMonthName }}</th>
                        <th colspan="3" class="px-3 py-2 text-center">Total {{ $filterYear }}</th>
                    </tr>
                    <tr class="bg-slate-50 text-slate-600 font-medium border-b border-slate-200 text-xs">
                        <th class="border-r border-slate-200 px-3 py-2">Target</th>
                        <th class="border-r border-slate-200 px-3 py-2">Realisasi</th>
                        <th class="border-r border-slate-200 px-3 py-2">%</th>
                        <th class="border-r border-slate-200 px-3 py-2">Target</th>
                        <th class="border-r border-slate-200 px-3 py-2">Realisasi</th>
                        <th class="border-r border-slate-200 px-3 py-2">%</th>
                        <th class="border-r border-slate-200 px-3 py-2">Target</th>
                        <th class="border-r border-slate-200 px-3 py-2">Realisasi</th>
                        <th class="px-3 py-2">%</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    @foreach($data['pivot_table']['by_entity'] as $ent)
                    <tr class="bg-white text-slate-700 border-b border-slate-100">
                        <td class="border-r border-slate-200 px-4 py-2 text-left sticky left-0 bg-white z-10">{{ $ent['name'] }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['prev']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['prev']['realization']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2 text-center">{!! formatAchPlain($ent['prev']['target'], $ent['prev']['realization']) !!}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['curr']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['curr']['realization']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2 text-center">{!! formatAchPlain($ent['curr']['target'], $ent['curr']['realization']) !!}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['total']['target']) }}</td>
                        <td class="border-r border-slate-200 px-3 py-2">{{ number_format($ent['total']['realization']) }}</td>
                        <td class="px-3 py-2 text-center">{!! formatAchPlain($ent['total']['target'], $ent['total']['realization']) !!}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-blue-50 font-bold text-slate-800 border-t border-slate-300">
                        <td class="border-r border-slate-300 px-4 py-3 text-left sticky left-0 bg-blue-50 z-10">Subtotal</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalPrevT) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalPrevR) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3 text-center">{!! formatAchPlain($totalPrevT, $totalPrevR) !!}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalCurrT) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalCurrR) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3 text-center">{!! formatAchPlain($totalCurrT, $totalCurrR) !!}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalTotT) }}</td>
                        <td class="border-r border-slate-300 px-3 py-3">{{ number_format($totalTotR) }}</td>
                        <td class="px-3 py-3 text-center">{!! formatAchPlain($totalTotT, $totalTotR) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Scripts for Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Chart.defaults.font.family = "'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
        Chart.defaults.color = '#64748b'; // slate-500
        Chart.defaults.scale.grid.color = '#f1f5f9'; // slate-100
        
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 4,
                    displayColors: true
                }
            }
        };

        const brightColors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ec4899', '#06b6d4', '#6366f1', '#14b8a6'];

        // 1. Team Chart (Bar)
        const teamCtx = document.getElementById('teamChart').getContext('2d');
        const teamData = @json($data['achievement_per_team']);
        new Chart(teamCtx, {
            type: 'bar',
            data: {
                labels: teamData.map(d => d.team),
                datasets: [
                    {
                        label: 'Target',
                        data: teamData.map(d => d.target),
                        backgroundColor: '#e2e8f0', // slate-200
                        borderRadius: 6,
                        barThickness: 16
                    },
                    {
                        label: 'Realisasi',
                        data: teamData.map(d => d.realization),
                        backgroundColor: teamData.map((_, i) => brightColors[i % brightColors.length]), // Berwarna warni
                        borderRadius: 6, // Bar melengkung
                        barThickness: 16
                    }
                ]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: { beginAtZero: true, grid: {drawBorder: false} },
                    x: { grid: {display: false} }
                }
            }
        });

        // 2. Entity Chart (Donut)
        const entityCtx = document.getElementById('entityChart').getContext('2d');
        const entityData = @json($data['achievement_per_entity']);
        new Chart(entityCtx, {
            type: 'doughnut',
            data: {
                labels: entityData.map(d => d.entity),
                datasets: [{
                    data: entityData.map(d => d.realization),
                    backgroundColor: brightColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 16,
                            font: { size: 11 }
                        }
                    },
                    tooltip: commonOptions.plugins.tooltip
                }
            }
        });

        // 3. Monthly Chart (Bar/Line)
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = @json($data['monthly_trend']);
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => `Bulan ${d.month}`),
                datasets: [
                    {
                        type: 'line',
                        label: 'Target',
                        data: monthlyData.map(d => d.target),
                        borderColor: '#94a3b8',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        tension: 0.3
                    },
                    {
                        type: 'bar',
                        label: 'Realisasi',
                        data: monthlyData.map(d => d.realization),
                        backgroundColor: '#3b82f6', // blue-500
                        borderRadius: 4,
                        barPercentage: 0.5
                    }
                ]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8 }
                    }
                },
                scales: {
                    y: { beginAtZero: true },
                    x: { grid: {display: false} }
                }
            }
        });

        // 4. Entity Line Charts (Grid)
        const entityTrends = @json($data['entity_trends']);
        entityTrends.forEach((trend, index) => {
            const el = document.getElementById(`entityLineChart_${index}`);
            if (el) {
                const ctx = el.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                        datasets: [
                            {
                                label: 'Target',
                                data: trend.data.map(d => d.target),
                                borderColor: '#cbd5e1', 
                                borderDash: [5, 5],
                                borderWidth: 2,
                                pointRadius: 0,
                                tension: 0.3
                            },
                            {
                                label: 'Realisasi',
                                data: trend.data.map(d => d.realization),
                                borderColor: brightColors[index % brightColors.length], 
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                pointRadius: 3,
                                pointBackgroundColor: '#ffffff',
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: commonOptions.plugins.tooltip
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                display: true, 
                                ticks: { maxTicksLimit: 5, font: {size: 10} }
                            }, 
                            x: { grid: {display: false}, ticks: { font: { size: 10 } } }
                        }
                    }
                });
            }
        });

    });
</script>
@endsection
