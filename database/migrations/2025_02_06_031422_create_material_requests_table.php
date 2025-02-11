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
        Schema::create('material_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('mr_doc_num');
            $table->date('mr_posting_date');
            $table->foreignId('warehouse_id')->constrained(table: 'warehouses')->restrictOnDelete();
            $table->foreignId('project_id')->constrained(table: 'projects')->restrictOnDelete();
            $table->foreignId('issue_purpose_id')->constrained(table: 'issue_purposes')->restrictOnDelete();
            $table->unsignedBigInteger('it_wo_id')->nullable();
            $table->text('mr_remarks')->nullable();
            $table->enum('mr_status', ['open', 'closed'])->default('open');
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
        Schema::dropIfExists('material_requests');
    }
};
