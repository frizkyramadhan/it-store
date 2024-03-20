<?php

namespace App\Imports;

use App\Models\Bouwheer;
use App\Models\Plant;
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

class BouwheerImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    private $plants;

    public function __construct()
    {
        $this->plants = Plant::select('id', 'plant_code')->get();
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        $get_plant = $this->plants->where('plant_code', $row['plant_code'])->first();

        $bouwheer = new Bouwheer;
        $bouwheer->plant_id = $get_plant->id;
        $bouwheer->bouwheer_name = $row['bouwheer_name'];
        $bouwheer->alias = $row['alias'] ?? NULL;
        $bouwheer->bouwheer_status = $row['bouwheer_status'];
        $bouwheer->bouwheer_remarks = $row['bouwheer_remarks'] ?? NULL;
        $bouwheer->save();
    }

    public function rules(): array
    {
        return [
            '*.plant_code' => ['required', 'exists:plants,plant_code'],
            '*.bouwheer_name' => ['required'],
            '*.bouwheer_status' => ['required', 'in:active,inactive'],
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
