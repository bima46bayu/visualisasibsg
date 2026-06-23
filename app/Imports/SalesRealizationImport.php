<?php

namespace App\Imports;

use App\Models\SalesRealization;
use App\Models\SalesMember;
use App\Models\Entity;
use App\Models\EndUser;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Validation\Rule;

class SalesRealizationImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => new SalesRealizationDataImport(),
        ];
    }
}

class SalesRealizationDataImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function prepareForValidation($data, $index)
    {
        $months = ['Januari'=>1, 'Februari'=>2, 'Maret'=>3, 'April'=>4, 'Mei'=>5, 'Juni'=>6, 'Juli'=>7, 'Agustus'=>8, 'September'=>9, 'Oktober'=>10, 'November'=>11, 'Desember'=>12];
        if (isset($data['bulan']) && !is_numeric($data['bulan'])) {
            $data['bulan'] = $months[ucfirst(strtolower(trim($data['bulan'])))] ?? $data['bulan'];
        }
        // Pastikan mapping end_user jika header excel bernama enduser
        if (!isset($data['end_user']) && isset($data['enduser'])) {
            $data['end_user'] = $data['enduser'];
        }
        return $data;
    }

    public function model(array $row)
    {
        $salesMember = SalesMember::where('name', $row['am'])->first();
        $entity = Entity::where('code', $row['entity'])->orWhere('name', $row['entity'])->first();
        
        $endUserName = isset($row['end_user']) ? trim($row['end_user']) : 'Umum';
        if (empty($endUserName)) {
            $endUserName = 'Umum';
        }
        $endUser = EndUser::firstOrCreate(['name' => $endUserName]);

        if (!$salesMember || !$entity) {
            return null; 
        }

        return SalesRealization::updateOrCreate(
            [
                'year' => $row['tahun'],
                'month' => $row['bulan'],
                'sales_member_id' => $salesMember->id,
                'entity_id' => $entity->id,
                'end_user_id' => $endUser->id,
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
            'end_user' => 'nullable',
            'realisasi' => 'required|numeric|min:0',
        ];
    }
}
