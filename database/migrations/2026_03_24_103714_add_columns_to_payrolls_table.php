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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('overtime_multiplier_used', 5, 2)->default(1.5)->after('total_hours');
            $table->json('attendance_ids')->nullable()->after('net_amount');
            $table->json('advance_ids')->nullable()->after('attendance_ids');
            $table->text('notes')->nullable()->after('advance_ids');
            $table->boolean('is_edited')->default(false)->after('notes');
            $table->timestamp('edited_at')->nullable()->after('is_edited');
            $table->text('edit_history')->nullable()->after('edited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'overtime_multiplier_used',
                'attendance_ids',
                'advance_ids',
                'notes',
                'is_edited',
                'edited_at',
                'edit_history'
            ]);
        });
    }
};
