<?php

namespace App\Http\Controllers;

use App\Models\BatchInventory;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function transactionIn($item_id, $warehouse_id, $quantity)
    {
        // Update stok di tabel Inventory
        $inventory = Inventory::where('item_id', $item_id)
            ->where('warehouse_id', $warehouse_id)
            ->first();

        if ($inventory) {
            $inventory->stock += $quantity;
            $inventory->save();
        } else {
            // Jika item dan warehouse tidak ada dalam inventori, buat entri baru
            $newInventory = new Inventory();
            $newInventory->item_id = $item_id;
            $newInventory->warehouse_id = $warehouse_id;
            $newInventory->stock = $quantity;
            $newInventory->save();
        }
    }

    public function transactionOut($item_id, $warehouse_id, $quantity)
    {
        $inventory = Inventory::where('item_id', $item_id)
            ->where('warehouse_id', $warehouse_id)
            ->first();

        if ($inventory) {
            if ($inventory->stock >= $quantity) {
                $inventory->stock -= $quantity;
                $inventory->save();
            } else {
                return response()->json(['message' => 'Stok tidak mencukupi.'], 400);
            }
        } else {
            return response()->json(['message' => 'Item dan warehouse tidak ada dalam inventori.'], 404);
        }
    }

    // checkStock for parsley.js validation
    public function checkStock(Request $request)
    {
        $gi_qty = $request->input('gi_qty');
        $mr_qty = $request->input('mr_qty');
        $trf_qty = $request->input('trf_qty');
        $warehouse_id = $request->input('warehouse_id');
        $item_id = $request->input('item_id');

        // Lakukan pemeriksaan stok di database
        $inventory = Inventory::where('item_id', $item_id)
            ->where('warehouse_id', $warehouse_id)
            ->first();

        // dd($inventory->stock);

        $stock = $inventory->stock; // Ganti 'stock' dengan nama kolom stok yang sesuai dalam model Anda

        // Beri respons berdasarkan hasil pemeriksaan stok
        if ($gi_qty) {
            if ($stock >= $gi_qty) {
                $output = [
                    'success' => true,
                    'stock' => $stock
                ];
                return response()->json($output);
            }
        } else if ($trf_qty) {
            if ($stock >= $trf_qty) {
                $output = [
                    'success' => true,
                    'stock' => $stock
                ];
                return response()->json($output);
            }
        } else if ($mr_qty) {
            if ($stock >= $mr_qty) {
                $output = [
                    'success' => true,
                    'stock' => $stock
                ];
                return response()->json($output);
            }
        }
    }

    public function checkStockByParams($item_id, $warehouse_id, $quantity)
    {
        // Cari stok berdasarkan item_id dan warehouse_id
        $inventory = Inventory::where('item_id', $item_id)
            ->where('warehouse_id', $warehouse_id)
            ->first();

        // Jika stok ada dan mencukupi, kembalikan true
        if ($inventory && $inventory->stock >= $quantity) {
            return true;
        }

        return false;
    }


    // public function batchIn($batch_id, $warehouse_id, $quantity)
    // {
    //     // Update stok di tabel Inventory
    //     $batchInventory = BatchInventory::where('batch_id', $batch_id)
    //         ->where('warehouse_id', $warehouse_id)
    //         ->first();

    //     if ($batchInventory) {
    //         $batchInventory->batch_stock += $quantity;
    //         $batchInventory->save();
    //     } else {
    //         // Jika batch dan warehouse tidak ada dalam inventori, buat entri baru
    //         $newBatchInventory = new BatchInventory();
    //         $newBatchInventory->batch_id = $batch_id;
    //         $newBatchInventory->warehouse_id = $warehouse_id;
    //         $newBatchInventory->batch_stock = $quantity;
    //         $newBatchInventory->save();
    //     }
    // }

    // public function batchOut($batch_id, $warehouse_id, $quantity)
    // {
    //     $batchInventory = BatchInventory::where('batch_id', $batch_id)
    //         ->where('warehouse_id', $warehouse_id)
    //         ->first();

    //     if ($batchInventory) {
    //         if ($batchInventory->batch_stock >= $quantity) {
    //             $batchInventory->batch_stock -= $quantity;
    //             $batchInventory->save();
    //         } else {
    //             return response()->json(['message' => 'Stok tidak mencukupi.'], 400);
    //         }
    //     } else {
    //         return response()->json(['message' => 'Batch dan warehouse tidak ada dalam inventori.'], 404);
    //     }
    // }
}
