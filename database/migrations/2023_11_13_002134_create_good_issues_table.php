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
        Schema::create('good_issues', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('gi_doc_num');
            $table->date('gi_posting_date');
            $table->foreignId('warehouse_id')->constrained(table: 'warehouses')->restrictOnDelete();
            $table->text('gi_remarks')->nullable();
            $table->enum('gi_status', ['open', 'closed'])->default('open');
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
        Schema::dropIfExists('good_issues');
    }
};
