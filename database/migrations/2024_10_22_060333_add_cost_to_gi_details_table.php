<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gi_details', function (Blueprint $table) {
            // Menambahkan kolom price setelah gi_qty
            $table->decimal('price', 15, 2)->after('gi_qty');
            $table->decimal('gi_line_total', 15, 2)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gi_details', function (Blueprint $table) {
            // Menghapus kolom price saat rollback
            $table->dropColumn('price');
            $table->dropColumn('gi_line_total');
        });
    }
};
