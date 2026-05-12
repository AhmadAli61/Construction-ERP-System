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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();

            $table->enum('rate_type', ['hourly', 'daily', 'monthly']);
            $table->decimal('rate_snapshot', 10, 2);

            $table->integer('total_days');
            $table->decimal('total_hours', 8, 2);

            $table->decimal('gross_amount', 12, 2);
            $table->decimal('advance_deduction', 12, 2)->default(0);
            $table->decimal('manual_adjustment', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2);

            $table->timestamps();

            $table->unique(['payroll_batch_id', 'worker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
