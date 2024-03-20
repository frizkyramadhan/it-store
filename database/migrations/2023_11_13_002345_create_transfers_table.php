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
        Schema::create('transfers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('trf_doc_num');
            $table->date('trf_posting_date');
            $table->enum('trf_type', ['out', 'in'])->default('out');
            $table->string('trf_ref_num')->nullable();
            $table->foreignId('trf_from')->constrained(table: 'warehouses')->restrictOnDelete();
            $table->foreignId('trf_to')->constrained(table: 'warehouses')->restrictOnDelete();
            $table->text('trf_remarks')->nullable();
            $table->enum('trf_status', ['open', 'closed'])->default('open');
            $table->enum('is_cancelled', ['yes', 'no'])->default('no');
            $table->foreignId('user_id')->constrained(table: 'users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
