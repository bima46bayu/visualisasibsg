<?php

namespace App\Imports;

use App\Models\SalesRealization;
use App\Models\SalesMember;
use App\Models\Entity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SalesRealizationImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        $months = ['Januari'=>1, 'Februari'=>2, 'Maret'=>3, 'April'=>4, 'Mei'=>5, 'Juni'=>6, 'Juli'=>7, 'Agustus'=>8, 'September'=>9, 'Oktober'=>10, 'November'=>11, 'Desember'=>12];
        if (isset($data['bulan']) && !is_numeric($data['bulan'])) {
            $data['bulan'] = $months[ucfirst(strtolower(trim($data['bulan'])))] ?? $data['bulan'];
        }
        return $data;
    }

    public function model(array $row)
    {
        $salesMember = SalesMember::where('name', $row['am'])->first();
        $entity = Entity::where('code', $row['entity'])->orWhere('name', $row['entity'])->first();

        if (!$salesMember || !$entity) {
            return null; 
        }

        return SalesRealization::updateOrCreate(
            [
                'year' => $row['tahun'],
                'month' => $row['bulan'],
                'sales_member_id' => $salesMember->id,
                'entity_id' => $entity->id,
            ],
            [
                'realization_amount' => $row['realisasi'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'tahun' => 'required|integer|min:2000',
            'bulan' => 'required|integer|min:1|max:12',
            'am' => ['required', function($attribute, $value, $fail) {
                if (!SalesMember::where('name', $value)->exists()) {
                    $fail('AM (Sales Member) tidak ditemukan.');
                }
            }],
            'entity' => ['required', function($attribute, $value, $fail) {
                if (!Entity::where('code', $value)->orWhere('name', $value)->exists()) {
                    $fail('Entity tidak ditemukan.');
                }
            }],
            'realisasi' => 'required|numeric|min:0',
        ];
    }
}
