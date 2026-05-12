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
        Schema::table('attendances', function (Blueprint $table) {
            // Add client billing type (how the company requested the worker)
            if (!Schema::hasColumn('attendances', 'client_billing_type')) {
                $table->enum('client_billing_type', ['daily', 'hourly'])->default('daily')->after('status');
            }

            // Add client hours (hours to bill the client - may differ from hours_worked)
            if (!Schema::hasColumn('attendances', 'client_hours')) {
                $table->decimal('client_hours', 5, 2)->nullable()->after('client_billing_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'client_billing_type')) {
                $table->dropColumn('client_billing_type');
            }
            if (Schema::hasColumn('attendances', 'client_hours')) {
                $table->dropColumn('client_hours');
            }
        });
    }
};
