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
        Schema::create('trf_details', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('transfer_id')->constrained(table: 'transfers')->restrictOnDelete();
            $table->foreignUlid('item_id')->constrained(table: 'items')->restrictOnDelete();
            $table->integer('trf_qty');
            $table->text('trf_line_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trf_details');
    }
};
