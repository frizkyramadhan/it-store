<?php

namespace App\Imports;

use App\Models\Bouwheer;
use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class WarehouseImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    private $bouwheers;

    public function __construct()
    {
        $this->bouwheers = Bouwheer::select('id', 'bouwheer_name')->get();
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        $get_bouwheer = $this->bouwheers->where('bouwheer_name', $row['bouwheer_name'])->first();

        $warehouse = new Warehouse;
        $warehouse->bouwheer_id = $get_bouwheer->id;
        $warehouse->warehouse_name = $row['warehouse_name'];
        $warehouse->warehouse_location = $row['warehouse_location'];
        $warehouse->warehouse_type = $row['warehouse_type'];
        $warehouse->warehouse_status = $row['warehouse_status'];
        $warehouse->save();
    }

    public function rules(): array
    {
        return [
            '*.bouwheer_name' => ['required', 'exists:bouwheers,bouwheer_name'],
            '*.warehouse_name' => ['required'],
            '*.warehouse_location' => ['required'],
            '*.warehouse_type' => ['required', 'in:main,transit'],
            '*.warehouse_status' => ['required', 'in:active,inactive'],
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
