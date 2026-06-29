<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Entity;
use App\Models\Profitability;

class ProfitabilityImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $months = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        // Group rows by Year, Month, and Entity
        $grouped = [];

        foreach ($rows as $row) {
            if (empty($row['tahun']) || empty($row['bulan']) || empty($row['entity'])) {
                continue; // Skip invalid rows
            }

            $year = $row['tahun'];
            $monthString = ucfirst(strtolower(trim($row['bulan'])));
            $month = $months[$monthString] ?? $row['bulan'];
            $entityName = trim($row['entity']);

            // Get or create entity
            $entity = Entity::firstOrCreate(
                ['name' => $entityName],
                ['code' => 'ENT-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $entityName), 0, 3)) . '-' . rand(1000, 9999)]
            );

            $key = "{$year}_{$month}_{$entity->id}";

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'year' => $year,
                    'month' => $month,
                    'entity_id' => $entity->id,
                    'items' => []
                ];
            }

            $category = strtolower(trim($row['kategori']));
            $amount = floatval($row['jumlah']);
            $desc = trim($row['deskripsi']);

            if (!empty($category)) {
                $grouped[$key]['items'][] = [
                    'category' => $category,
                    'description' => $desc,
                    'amount' => $amount
                ];
            }
        }

        // Process each group and save to database
        foreach ($grouped as $group) {
            // Find existing profitability or create new
            $prof = Profitability::firstOrCreate(
                [
                    'year' => $group['year'],
                    'month' => $group['month'],
                    'entity_id' => $group['entity_id']
                ]
            );

            // Wipe existing items to replace with new excel data
            $prof->items()->delete();
            $prof->items()->createMany($group['items']);

            // Recalculate totals
            $totalPendapatan = $prof->items()->where('category', 'pendapatan')->sum('amount');
            $totalHpp = $prof->items()->where('category', 'hpp')->sum('amount');
            $labaKotor = $totalPendapatan - $totalHpp;

            $biayaMarketing = $prof->items()->where('category', 'biaya_marketing')->sum('amount');
            $biayaAdmin = $prof->items()->where('category', 'biaya_admin')->sum('amount');
            $biayaNonOps = $prof->items()->where('category', 'biaya_non_ops')->sum('amount');
            $totalOverhead = $biayaMarketing + $biayaAdmin + $biayaNonOps;

            $labaOperasi = $labaKotor - $totalOverhead;

            $pendapatanLain = $prof->items()->where('category', 'pendapatan_lain')->sum('amount');
            $biayaLain = $prof->items()->where('category', 'biaya_lain')->sum('amount');
            $pajak = $prof->items()->where('category', 'pajak')->sum('amount');

            $labaSebelumPajak = $labaOperasi + $pendapatanLain - $biayaLain;
            $labaBersih = $labaSebelumPajak - $pajak;

            $prof->update([
                'pendapatan' => $totalPendapatan,
                'laba_kotor' => $labaKotor,
                'total_biaya_overhead' => $totalOverhead,
                'laba_operasi' => $labaOperasi,
                'laba_sebelum_pajak' => $labaSebelumPajak,
                'laba_bersih' => $labaBersih,
            ]);
        }
    }
}
