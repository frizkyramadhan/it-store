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
        Schema::create('mr_details', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('material_request_id')->constrained(table: 'material_requests')->restrictOnDelete();
            $table->foreignUlid('item_id')->constrained(table: 'items')->restrictOnDelete();
            $table->integer('mr_qty');
            $table->text('mr_line_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mr_details');
    }
};
