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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('project_code')->unique();

            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();

            $table->text('location')->nullable();
            $table->text('description')->nullable();

            $table->decimal('contract_value', 15, 2)->default(0);
            $table->decimal('estimated_cost', 15, 2)->default(0);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->enum('status', [
                'planning',
                'ongoing',
                'on_hold',
                'completed',
                'cancelled'
            ])->default('planning');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
