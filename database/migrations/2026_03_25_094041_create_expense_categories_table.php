<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->default('fas fa-receipt');
            $table->string('color')->default('#6c757d');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default categories
        DB::table('expense_categories')->insert([
            ['name' => 'Travel', 'icon' => 'fas fa-plane', 'color' => '#3b82f6', 'sort_order' => 1],
            ['name' => 'Equipment', 'icon' => 'fas fa-tools', 'color' => '#10b981', 'sort_order' => 2],
            ['name' => 'Materials', 'icon' => 'fas fa-cubes', 'color' => '#f59e0b', 'sort_order' => 3],
            ['name' => 'Subcontractors', 'icon' => 'fas fa-handshake', 'color' => '#8b5cf6', 'sort_order' => 4],
            ['name' => 'Utilities', 'icon' => 'fas fa-bolt', 'color' => '#ef4444', 'sort_order' => 5],
            ['name' => 'Office Supplies', 'icon' => 'fas fa-boxes', 'color' => '#ec489a', 'sort_order' => 6],
            ['name' => 'Fuel', 'icon' => 'fas fa-gas-pump', 'color' => '#14b8a6', 'sort_order' => 7],
            ['name' => 'Maintenance', 'icon' => 'fas fa-wrench', 'color' => '#f97316', 'sort_order' => 8],
            ['name' => 'Permits & Licenses', 'icon' => 'fas fa-file-contract', 'color' => '#a855f7', 'sort_order' => 9],
            ['name' => 'Insurance', 'icon' => 'fas fa-shield-alt', 'color' => '#06b6d4', 'sort_order' => 10],
            ['name' => 'Others', 'icon' => 'fas fa-ellipsis-h', 'color' => '#6b7280', 'sort_order' => 99],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
