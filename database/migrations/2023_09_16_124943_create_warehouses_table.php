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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bouwheer_id')->constrained(table: 'bouwheers')->restrictOnDelete();
            $table->string('warehouse_name');
            $table->string('warehouse_location');
            $table->enum('warehouse_type', ['main', 'transit'])->default('main');
            $table->enum('warehouse_status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
