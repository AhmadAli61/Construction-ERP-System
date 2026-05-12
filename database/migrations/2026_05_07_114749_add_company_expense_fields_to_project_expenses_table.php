<?php
// database/migrations/2024_01_01_000001_add_company_expense_fields_to_project_expenses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyExpenseFieldsToProjectExpensesTable extends Migration
{
    public function up()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->enum('expense_type', ['project', 'company'])->default('project')->after('notes');
            $table->string('invoice_number')->nullable()->unique()->after('expense_type');
        });
    }

    public function down()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn(['expense_type', 'invoice_number']);
        });
    }
}