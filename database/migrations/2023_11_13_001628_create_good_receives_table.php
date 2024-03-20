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
        Schema::create('good_receives', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('gr_doc_num');
            $table->date('gr_posting_date');
            $table->foreignId('vendor_id')->constrained(table: 'vendors')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained(table: 'warehouses')->restrictOnDelete();
            $table->text('gr_remarks')->nullable();
            $table->enum('gr_status', ['open', 'closed'])->default('open');
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
        Schema::dropIfExists('good_receives');
    }
};
