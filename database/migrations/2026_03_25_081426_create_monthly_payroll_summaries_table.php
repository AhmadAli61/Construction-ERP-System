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
        Schema::create('monthly_payroll_summaries', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->decimal('total_gross_amount', 15, 2)->default(0);
            $table->decimal('total_net_amount', 15, 2)->default(0);
            $table->decimal('total_advances_deducted', 15, 2)->default(0);
            $table->integer('total_workers')->default(0);
            $table->integer('total_payrolls')->default(0);
            $table->integer('single_worker_payrolls')->default(0);
            $table->integer('batch_payrolls')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('saved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('saved_at')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_payroll_summaries');
    }
};
