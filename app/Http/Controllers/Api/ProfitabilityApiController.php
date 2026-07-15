<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profitability;
use App\Models\Entity;
use App\Models\ProfitabilitySubEntity;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitabilityExport;
use App\Imports\ProfitabilityImport;

class ProfitabilityApiController extends Controller
{
    private function mapItemsToFrontend($profitability)
    {
        $items = $profitability->items;
        $profitability->pendapatan_items = $items->where('category', 'pendapatan')->values();
        $profitability->hpp_items = $items->where('category', 'hpp')->values();
        $profitability->biaya_marketing_items = $items->where('category', 'biaya_marketing')->values();
        $profitability->biaya_admin_items = $items->where('category', 'biaya_admin')->values();
        $profitability->biaya_non_ops_items = $items->where('category', 'biaya_non_ops')->values();
        $profitability->pendapatan_lain_items = $items->where('category', 'pendapatan_lain')->values();
        $profitability->biaya_lain_items = $items->where('category', 'biaya_lain')->values();
        $profitability->pajak_items = $items->where('category', 'pajak')->values();
        
        unset($profitability->items);
        return $profitability;
    }

    public function getSubEntities()
    {
        $subEntities = ProfitabilitySubEntity::with('entity')->get();
        return response()->json($subEntities);
    }

    public function storeSubEntity(Request $request)
    {
        $data = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'name' => 'required|string',
            'code' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $subEntity = ProfitabilitySubEntity::create($data);
        return response()->json($subEntity, 201);
    }

    public function updateSubEntity(Request $request, $id)
    {
        $data = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'name' => 'required|string',
            'code' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $subEntity = ProfitabilitySubEntity::findOrFail($id);
        $subEntity->update($data);
        return response()->json($subEntity);
    }

    public function destroySubEntity($id)
    {
        ProfitabilitySubEntity::findOrFail($id)->delete();
        return response()->json(['message' => 'Sub Entity deleted successfully']);
    }

    public function getDescriptions()
    {
        $descriptions = \App\Models\ProfitabilityDescription::all();
        return response()->json($descriptions);
    }

    public function storeDescription(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'name' => 'required|string',
        ]);

        $existing = \App\Models\ProfitabilityDescription::where('category', $data['category'])
            ->where('name', $data['name'])
            ->first();
            
        if ($existing) {
            return response()->json($existing, 200);
        }

        $description = \App\Models\ProfitabilityDescription::create($data);
        return response()->json($description, 201);
    }

    public function updateDescription(Request $request, $id)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'name' => 'required|string',
        ]);

        $description = \App\Models\ProfitabilityDescription::findOrFail($id);
        $description->update($data);
        return response()->json($description);
    }

    public function destroyDescription($id)
    {
        \App\Models\ProfitabilityDescription::findOrFail($id)->delete();
        return response()->json(['message' => 'Description deleted successfully']);
    }

    public function index(Request $request)
    {
        $query = Profitability::with(['entity', 'subEntity', 'items']);
        
        if ($request->year) $query->where('year', $request->year);
        if ($request->month) $query->where('month', $request->month);
        
        $paginator = $query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10);
        
        $paginator->getCollection()->transform(function ($prof) {
            return $this->mapItemsToFrontend($prof);
        });

        return response()->json($paginator);
    }

    private function calculateEntityMargin($profitabilities)
    {
        $entityMargin = [];
        $entities = Entity::with('subEntities')->get();
        foreach ($entities as $entity) {
            $entityData = $profitabilities->where('entity_id', $entity->id);
            if ($entityData->isEmpty()) continue;

            $totalPendapatan = $entityData->sum('pendapatan');
            $totalLabaKotor = $entityData->sum('laba_kotor');
            $totalLabaBersih = $entityData->sum('laba_bersih');
            $totalHpp = 0;
            foreach ($entityData as $ed) {
                $totalHpp += $ed->items->where('category', 'hpp')->sum('amount');
            }

            $totalOverhead = $entityData->sum('total_biaya_overhead');
            $totalLabaSebelumPajak = $entityData->sum('laba_sebelum_pajak');

            $mainEntityRow = [
                'entity' => $entity->name,
                'revenue' => $totalPendapatan,
                'cogs' => $totalHpp,
                'gross_profit' => $totalLabaKotor,
                'overhead' => $totalOverhead,
                'laba_sebelum_pajak' => $totalLabaSebelumPajak,
                'gross_margin' => $totalPendapatan > 0 ? round(($totalLabaKotor / $totalPendapatan) * 100, 2) : 0,
                'net_margin' => $totalPendapatan > 0 ? round(($totalLabaBersih / $totalPendapatan) * 100, 2) : 0,
                'subRows' => []
            ];

            if ($entity->subEntities->count() > 0) {
                foreach ($entity->subEntities as $subEntity) {
                    $subData = $entityData->where('sub_entity_id', $subEntity->id);
                    if ($subData->isEmpty()) continue;

                    $subPendapatan = $subData->sum('pendapatan');
                    $subLabaKotor = $subData->sum('laba_kotor');
                    $subOverhead = $subData->sum('total_biaya_overhead');
                    $subLabaSebelumPajak = $subData->sum('laba_sebelum_pajak');
                    $subLabaBersih = $subData->sum('laba_bersih');
                    $subHpp = 0;
                    foreach ($subData as $ed) {
                        $subHpp += $ed->items->where('category', 'hpp')->sum('amount');
                    }

                    $mainEntityRow['subRows'][] = [
                        'id' => $entity->id . '-' . $subEntity->id,
                        'entity' => $subEntity->name,
                        'revenue' => $subPendapatan,
                        'cogs' => $subHpp,
                        'gross_profit' => $subLabaKotor,
                        'overhead' => $subOverhead,
                        'laba_sebelum_pajak' => $subLabaSebelumPajak,
                        'gross_margin' => $subPendapatan > 0 ? round(($subLabaKotor / $subPendapatan) * 100, 2) : 0,
                        'net_margin' => $subPendapatan > 0 ? round(($subLabaBersih / $subPendapatan) * 100, 2) : 0,
                    ];
                }
                
                $mainEntityOnlyData = $entityData->whereNull('sub_entity_id');
                if ($mainEntityOnlyData->isNotEmpty()) {
                    $subPendapatan = $mainEntityOnlyData->sum('pendapatan');
                    $subLabaKotor = $mainEntityOnlyData->sum('laba_kotor');
                    $subOverhead = $mainEntityOnlyData->sum('total_biaya_overhead');
                    $subLabaSebelumPajak = $mainEntityOnlyData->sum('laba_sebelum_pajak');
                    $subLabaBersih = $mainEntityOnlyData->sum('laba_bersih');
                    $subHpp = 0;
                    foreach ($mainEntityOnlyData as $ed) {
                        $subHpp += $ed->items->where('category', 'hpp')->sum('amount');
                    }
                    
                    $mainEntityRow['subRows'][] = [
                        'id' => $entity->id . '-main',
                        'entity' => $entity->name . ' (Pusat)',
                        'revenue' => $subPendapatan,
                        'cogs' => $subHpp,
                        'gross_profit' => $subLabaKotor,
                        'overhead' => $subOverhead,
                        'laba_sebelum_pajak' => $subLabaSebelumPajak,
                        'gross_margin' => $subPendapatan > 0 ? round(($subLabaKotor / $subPendapatan) * 100, 2) : 0,
                        'net_margin' => $subPendapatan > 0 ? round(($subLabaBersih / $subPendapatan) * 100, 2) : 0,
                    ];
                }
            }
            if (empty($mainEntityRow['subRows'])) {
                unset($mainEntityRow['subRows']);
            }
            $entityMargin[] = $mainEntityRow;
        }
        usort($entityMargin, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        return $entityMargin;
    }

    public function dashboard(Request $request)
    {
        $year = $request->year ?: date('Y');
        $month = $request->month;
        $chartMonth = $request->chart_month;
        $tableMonth = $request->table_month;

        $yearlyProfitabilities = Profitability::with('items')->where('year', $year)->get();
        
        $trendData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $yearlyProfitabilities->where('month', $i);
            $trendData[] = [
                'month' => $i,
                'pendapatan' => $monthData->sum('pendapatan'),
                'laba_kotor' => $monthData->sum('laba_kotor'),
                'laba_bersih' => $monthData->sum('laba_bersih'),
            ];
        }

        $profitabilities = $month ? $yearlyProfitabilities->where('month', (int)$month) : $yearlyProfitabilities;
        $chartProfitabilities = $chartMonth ? $yearlyProfitabilities->where('month', (int)$chartMonth) : $yearlyProfitabilities;
        $tableProfitabilities = $tableMonth ? $yearlyProfitabilities->where('month', (int)$tableMonth) : $yearlyProfitabilities;

        $totalPendapatan = $profitabilities->sum('pendapatan');
        $totalLabaKotor = $profitabilities->sum('laba_kotor');
        $totalLabaOperasi = $profitabilities->sum('laba_operasi');
        $totalLabaSebelumPajak = $profitabilities->sum('laba_sebelum_pajak');
        $totalLabaBersih = $profitabilities->sum('laba_bersih');

        $totalHPP = 0;
        $totalMarketing = 0;
        $totalAdmin = 0;
        $totalNonOps = 0;
        $totalPajak = 0;

        foreach ($profitabilities as $p) {
            $totalHPP += $p->items->where('category', 'hpp')->sum('amount');
            $totalMarketing += $p->items->where('category', 'biaya_marketing')->sum('amount');
            $totalAdmin += $p->items->where('category', 'biaya_admin')->sum('amount');
            $totalNonOps += $p->items->where('category', 'biaya_non_ops')->sum('amount');
            $totalPajak += $p->items->where('category', 'pajak')->sum('amount');
        }

        $costAllocation = [
            ['name' => 'HPP (COGS)', 'value' => $totalHPP, 'color' => '#DCF26B'],
            ['name' => 'Marketing', 'value' => $totalMarketing, 'color' => '#C2EAD4'],
            ['name' => 'Admin', 'value' => $totalAdmin, 'color' => '#BFE0F2'],
            ['name' => 'Non-Ops & Lain', 'value' => $totalNonOps, 'color' => '#F4D9C2'],
            ['name' => 'Pajak', 'value' => $totalPajak, 'color' => '#FFB6C1'],
        ];

        return response()->json([
            'summary' => [
                'pendapatan' => $totalPendapatan,
                'laba_kotor' => $totalLabaKotor,
                'laba_operasi' => $totalLabaOperasi,
                'laba_sebelum_pajak' => $totalLabaSebelumPajak,
                'laba_bersih' => $totalLabaBersih,
                'hpp' => $totalHPP,
                'overhead' => $totalMarketing + $totalAdmin + $totalNonOps,
            ],
            'trend' => $trendData,
            'cost_allocation' => array_values(array_filter($costAllocation, fn($c) => $c['value'] > 0)),
            'entity_margin_chart' => $this->calculateEntityMargin($chartProfitabilities),
            'entity_margin_table' => $this->calculateEntityMargin($tableProfitabilities),
            'year' => $year
        ]);
    }

    public function getMonthlyMatrix(Request $request)
    {
        $year = $request->year ?: date('Y');
        $entityParam = $request->entity_id;
        
        $entityId = null;
        $subEntityId = null;

        if ($entityParam) {
            if (strpos($entityParam, '|') !== false) {
                list($entityId, $subEntityId) = explode('|', $entityParam);
            } else {
                $entityId = $entityParam;
            }
        }

        if (!$entityId) {
            $firstEntity = Entity::first();
            if ($firstEntity) {
                $entityId = $firstEntity->id;
                // If it has sub entities, default to the first sub entity
                $firstSub = ProfitabilitySubEntity::where('entity_id', $entityId)->first();
                if ($firstSub) {
                    $subEntityId = $firstSub->id;
                }
            } else {
                return response()->json([]);
            }
        }

        // Get profitability records for this entity, sub-entity, and year
        $query = Profitability::with('items')
            ->where('year', $year)
            ->where('entity_id', $entityId);

        if ($subEntityId) {
            $query->where('sub_entity_id', $subEntityId);
        } else {
            $query->whereNull('sub_entity_id');
        }

        $records = $query->get();

        // We want to construct a matrix of categories and months (1 to 12)
        $categories = [
            'pendapatan' => 'Pendapatan',
            'hpp' => 'Harga Pokok Penjualan (HPP)',
            'laba_kotor' => 'Laba Kotor',
            'biaya_marketing' => 'Biaya Marketing',
            'biaya_admin' => 'Biaya Admin & Umum',
            'biaya_non_ops' => 'Biaya Non Operasional',
            'total_biaya_overhead' => 'Total Biaya Overhead',
            'laba_operasi' => 'Laba Operasi',
            'pendapatan_lain' => 'Pendapatan Lain',
            'biaya_lain' => 'Biaya Lain (Bunga, dll)',
            'laba_sebelum_pajak' => 'Laba Bersih Sebelum Pajak',
            'pajak' => 'Pajak',
            'laba_bersih' => 'Laba Bersih Setelah Pajak',
        ];

        $matrix = [];
        foreach ($categories as $key => $label) {
            $row = [
                'category' => $key,
                'description' => $label,
            ];
            for ($m = 1; $m <= 12; $m++) {
                $record = $records->where('month', $m)->first();
                
                $value = 0;
                if ($record) {
                    if ($key === 'laba_kotor') {
                        $value = $record->laba_kotor;
                    } elseif ($key === 'total_biaya_overhead') {
                        $value = $record->total_biaya_overhead;
                    } elseif ($key === 'laba_operasi') {
                        $value = $record->laba_operasi;
                    } elseif ($key === 'laba_sebelum_pajak') {
                        $value = $record->laba_sebelum_pajak;
                    } elseif ($key === 'laba_bersih') {
                        $value = $record->laba_bersih;
                    } elseif ($key === 'pendapatan') {
                        $value = $record->pendapatan;
                    } else {
                        $value = $record->items->where('category', $key)->sum('amount');
                    }
                }
                $row['m' . $m] = $value;
            }
            $matrix[] = $row;
        }

        return response()->json($matrix);
    }

    private function syncItems($profitability, $data)
    {
        $profitability->items()->delete();
        $categories = [
            'pendapatan_items' => 'pendapatan',
            'hpp_items' => 'hpp',
            'biaya_marketing_items' => 'biaya_marketing',
            'biaya_admin_items' => 'biaya_admin',
            'biaya_non_ops_items' => 'biaya_non_ops',
            'pendapatan_lain_items' => 'pendapatan_lain',
            'biaya_lain_items' => 'biaya_lain',
            'pajak_items' => 'pajak'
        ];

        $insertData = [];
        foreach ($categories as $key => $category) {
            if (!empty($data[$key]) && is_array($data[$key])) {
                foreach ($data[$key] as $item) {
                    if (!isset($item['description']) && !isset($item['amount'])) continue;
                    $insertData[] = [
                        'category' => $category,
                        'description' => $item['description'] ?? '',
                        'amount' => $item['amount'] ?? 0,
                    ];
                }
            }
        }

        if (!empty($insertData)) {
            $profitability->items()->createMany($insertData);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'drafts' => 'required|array',
            'drafts.*.year' => 'required|integer',
            'drafts.*.month' => 'required|integer',
            'drafts.*.entity_id' => 'required',
            'drafts.*.sub_entity_id' => 'nullable',
            'drafts.*.pendapatan_items' => 'nullable|array',
            'drafts.*.hpp_items' => 'nullable|array',
            'drafts.*.biaya_marketing_items' => 'nullable|array',
            'drafts.*.biaya_admin_items' => 'nullable|array',
            'drafts.*.biaya_non_ops_items' => 'nullable|array',
            'drafts.*.pendapatan_lain_items' => 'nullable|array',
            'drafts.*.biaya_lain_items' => 'nullable|array',
            'drafts.*.pajak_items' => 'nullable|array',
            'drafts.*.pendapatan' => 'nullable|numeric',
            'drafts.*.laba_kotor' => 'nullable|numeric',
            'drafts.*.total_biaya_overhead' => 'nullable|numeric',
            'drafts.*.laba_operasi' => 'nullable|numeric',
            'drafts.*.laba_sebelum_pajak' => 'nullable|numeric',
            'drafts.*.laba_bersih' => 'nullable|numeric',
        ]);

        foreach ($data['drafts'] as $d) {
            $payload = [
                'year' => $d['year'],
                'month' => $d['month'],
                'entity_id' => $d['entity_id'],
                'sub_entity_id' => $d['sub_entity_id'] ?? null,
                'pendapatan' => $d['pendapatan'] ?? 0,
                'laba_kotor' => $d['laba_kotor'] ?? 0,
                'total_biaya_overhead' => $d['total_biaya_overhead'] ?? 0,
                'laba_operasi' => $d['laba_operasi'] ?? 0,
                'laba_sebelum_pajak' => $d['laba_sebelum_pajak'] ?? 0,
                'laba_bersih' => $d['laba_bersih'] ?? 0,
            ];

            if (isset($d['id']) && !empty($d['id'])) {
                $prof = Profitability::find($d['id']);
                if ($prof) {
                    $prof->update($payload);
                    $this->syncItems($prof, $d);
                    continue;
                }
            }

            $prof = Profitability::create($payload);
            $this->syncItems($prof, $d);
        }

        return response()->json(['message' => 'Profitability saved successfully']);
    }

    public function update(Request $request, $id)
    {
        $d = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'entity_id' => 'required',
            'sub_entity_id' => 'nullable',
            'pendapatan_items' => 'nullable|array',
            'hpp_items' => 'nullable|array',
            'biaya_marketing_items' => 'nullable|array',
            'biaya_admin_items' => 'nullable|array',
            'biaya_non_ops_items' => 'nullable|array',
            'pendapatan_lain_items' => 'nullable|array',
            'biaya_lain_items' => 'nullable|array',
            'pajak_items' => 'nullable|array',
            'pendapatan' => 'nullable|numeric',
            'laba_kotor' => 'nullable|numeric',
            'total_biaya_overhead' => 'nullable|numeric',
            'laba_operasi' => 'nullable|numeric',
            'laba_sebelum_pajak' => 'nullable|numeric',
            'laba_bersih' => 'nullable|numeric',
        ]);

        $prof = Profitability::findOrFail($id);
        $prof->update([
            'year' => $d['year'],
            'month' => $d['month'],
            'entity_id' => $d['entity_id'],
            'sub_entity_id' => $d['sub_entity_id'] ?? null,
            'pendapatan' => $d['pendapatan'] ?? 0,
            'laba_kotor' => $d['laba_kotor'] ?? 0,
            'total_biaya_overhead' => $d['total_biaya_overhead'] ?? 0,
            'laba_operasi' => $d['laba_operasi'] ?? 0,
            'laba_sebelum_pajak' => $d['laba_sebelum_pajak'] ?? 0,
            'laba_bersih' => $d['laba_bersih'] ?? 0,
        ]);

        $this->syncItems($prof, $d);

        return response()->json(['message' => 'Profitability updated successfully']);
    }

    public function destroy($id)
    {
        Profitability::findOrFail($id)->delete();
        return response()->json(['message' => 'Profitability deleted successfully']);
    }

    public function export(Request $request)
    {
        return Excel::download(new ProfitabilityExport(false), 'profitability_data.xlsx');
    }

    public function exportTemplate(Request $request)
    {
        return Excel::download(new ProfitabilityExport(true), 'profitability_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
        // Simpan file sementara ke local storage untuk menghindari error open_basedir pada hosting
        $path = $request->file('file')->store('temp', 'local');
        
        // Nonaktifkan E_WARNING sementara karena PhpSpreadsheet sering memicu 
        // open_basedir warning saat memeriksa path file di dalam ZIP (misal: /xl/worksheets/...)
        $oldErrorLevel = error_reporting(E_ALL & ~E_WARNING);
        
        try {
            Excel::import(new ProfitabilityImport(), $path, 'local');
        } finally {
            // Kembalikan level error reporting ke semula
            error_reporting($oldErrorLevel);
            // Hapus file setelah import selesai
            \Illuminate\Support\Facades\Storage::disk('local')->delete($path);
        }

        return response()->json(['message' => 'Data profitability berhasil diimpor']);
    }
}
