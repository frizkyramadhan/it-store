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
        Schema::table('good_issues', function (Blueprint $table) {
            // Menambahkan kolom project_id setelah warehouse_id
            $table->unsignedBigInteger('project_id')->after('warehouse_id')->nullable();
            // Menambahkan kolom issue_purpose_id setelah project_id
            $table->unsignedBigInteger('issue_purpose_id')->after('project_id')->nullable();

            // Menambahkan foreign key untuk project_id
            $table->foreign('project_id')->references('id')->on('projects')->restrictOnDelete();
            // Menambahkan foreign key untuk issue_purpose_id
            $table->foreign('issue_purpose_id')->references('id')->on('issue_purposes')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('good_issues', function (Blueprint $table) {
            // Menghapus foreign key dan kolom project_id
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');

            // Menghapus foreign key dan kolom issue_purpose_id
            $table->dropForeign(['issue_purpose_id']);
            $table->dropColumn('issue_purpose_id');
        });
    }
};
