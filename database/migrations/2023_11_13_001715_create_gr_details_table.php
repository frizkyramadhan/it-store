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
        Schema::create('gr_details', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('good_receive_id')->constrained(table: 'good_receives')->restrictOnDelete();
            $table->foreignUlid('item_id')->constrained(table: 'items')->restrictOnDelete();
            $table->integer('gr_qty');
            $table->text('gr_line_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gr_details');
    }
};
