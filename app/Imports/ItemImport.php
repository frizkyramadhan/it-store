<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Type;
use App\Models\Group;
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

class ItemImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    private $types, $groups;

    public function __construct()
    {
        // $this->types = Type::select('id', 'type_name')->get();
        $this->groups = Group::select('id', 'group_name')->get();
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        // $get_type = $this->types->where('type_name', $row['type_name'])->first();
        $get_group = $this->groups->where('group_name', $row['group_name'])->first();

        $item = new Item;
        $item->item_code = $row['item_code'];
        $item->description = $row['description'];
        // $item->type_id = $get_type->id;
        $item->group_id = $get_group->id;
        // $item->weight_ea = $row['weight_ea'] ?? NULL;
        // $item->dims_l = $row['dims_l'] ?? NULL;
        // $item->dims_w = $row['dims_w'] ?? NULL;
        // $item->dims_h = $row['dims_h'] ?? NULL;
        // $item->nec_ea = $row['nec_ea'] ?? NULL;
        // $item->nec_box = $row['nec_box'] ?? NULL;
        // $item->gw_box = $row['gw_box'] ?? NULL;
        // $item->nw_box = $row['nw_box'] ?? NULL;
        // $item->un_no = $row['un_no'] ?? NULL;
        // $item->classification = $row['classification'] ?? NULL;
        // $item->ex = $row['ex'] ?? NULL;
        // $item->manu_from = $row['manu_from'] ?? NULL;
        // $item->shelf_life = $row['shelf_life'] ?? NULL;
        // $item->is_batch = $row['is_batch'];
        $item->item_status = $row['item_status'];
        $item->save();
    }

    public function rules(): array
    {
        return [
            '*.item_code' => ['required', 'unique:items,item_code'],
            '*.description' => ['required'],
            // '*.type_name' => ['required', 'exists:types,type_name'],
            '*.group_name' => ['required', 'exists:groups,group_name'],
            // '*.is_batch' => ['required', 'in:yes,no'],
            '*.item_status' => ['required', 'in:active,inactive'],
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
