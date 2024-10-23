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
            $table->unsignedBigInteger('it_wo_id')->after('issue_purpose_id')->nullable();
            $table->decimal('total_cost', 15, 2)->after('it_wo_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('good_issues', function (Blueprint $table) {
            $table->dropColumn(['it_wo_id', 'total_cost']);
        });
    }
};
