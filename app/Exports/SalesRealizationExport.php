<?php

namespace App\Exports;

use App\Models\SalesRealization;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Builder;

class SalesRealizationExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startYear;
    protected $startMonth;
    protected $endYear;
    protected $endMonth;

    public function __construct($startYear = null, $startMonth = null, $endYear = null, $endMonth = null)
    {
        $this->startYear = $startYear;
        $this->startMonth = $startMonth;
        $this->endYear = $endYear;
        $this->endMonth = $endMonth;
    }

    public function query()
    {
        $query = SalesRealization::query()->with(['salesMember', 'entity', 'endUser']);

        if ($this->startYear && $this->startMonth) {
            $query->where(function (Builder $q) {
                $q->where('year', '>', $this->startYear)
                  ->orWhere(function (Builder $q) {
                      $q->where('year', '=', $this->startYear)
                        ->where('month', '>=', $this->startMonth);
                  });
            });
        }

        if ($this->endYear && $this->endMonth) {
            $query->where(function (Builder $q) {
                $q->where('year', '<', $this->endYear)
                  ->orWhere(function (Builder $q) {
                      $q->where('year', '=', $this->endYear)
                        ->where('month', '<=', $this->endMonth);
                  });
            });
        }

        return $query->orderBy('year', 'desc')->orderBy('month', 'desc');
    }

    public function headings(): array
    {
        return [
            'tahun',
            'bulan',
            'am',
            'entity',
            'end_user',
            'realisasi',
        ];
    }

    public function map($realization): array
    {
        $months_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return [
            $realization->year,
            isset($months_names[$realization->month - 1]) ? $months_names[$realization->month - 1] : $realization->month,
            $realization->salesMember ? $realization->salesMember->name : null,
            $realization->entity ? $realization->entity->name : null,
            $realization->endUser ? $realization->endUser->name : null,
            $realization->realization_amount,
        ];
    }
}
