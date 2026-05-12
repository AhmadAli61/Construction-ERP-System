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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('quotation_number')->nullable()->after('project_code');
            $table->string('client_address')->nullable()->after('client_email');
            $table->date('valid_until')->nullable()->after('end_date');
            $table->decimal('vat_rate', 5, 2)->default(0)->after('estimated_cost');
            $table->boolean('vat_included')->default(false)->after('vat_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['quotation_number', 'client_address', 'valid_until', 'vat_rate', 'vat_included']);
        });
    }
};
