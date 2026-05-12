<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_advances', function (Blueprint $table) {
            // Add columns one by one in correct order
            $table->foreignId('parent_advance_id')->nullable()->after('id')->constrained('worker_advances')->nullOnDelete();
            $table->boolean('is_deduction')->default(false)->after('amount');
            $table->decimal('running_balance', 10, 2)->default(0)->after('remaining_amount');
            $table->foreignId('deducted_in_payroll_id')->nullable()->after('running_balance')->constrained('payroll_batches')->nullOnDelete();
            $table->timestamp('payroll_generated_at')->nullable()->after('deducted_in_payroll_id');
        });
    }

    public function down(): void
    {
        Schema::table('worker_advances', function (Blueprint $table) {
            $table->dropForeign(['parent_advance_id']);
            $table->dropForeign(['deducted_in_payroll_id']);
            $table->dropColumn([
                'parent_advance_id',
                'is_deduction',
                'running_balance',
                'deducted_in_payroll_id',
                'payroll_generated_at'
            ]);
        });
    }
};
