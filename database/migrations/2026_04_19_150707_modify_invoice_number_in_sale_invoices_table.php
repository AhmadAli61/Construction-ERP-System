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
        // First drop the unique constraint
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']);
        });
        
        // Then modify the column to be nullable initially (optional)
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->string('invoice_number')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the unique constraint
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->unique('invoice_number');
        });
    }
};