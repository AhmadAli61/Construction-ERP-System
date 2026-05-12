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
        Schema::create('sale_invoices', function (Blueprint $table) {
$table->id();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('unpaid');

            // Company details (hardcoded service provider)
            $table->string('company_name')->default('Your company');
            $table->string('company_address')->default('Calle Madura 10 02 dr Guipuzkoa, Bergara, Spain');
            $table->string('company_cif')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();

            // Client/Project details
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('client_address')->nullable();

            // Financial summary
            $table->decimal('subtotal', 12, 2);
            $table->decimal('vat_percentage', 5, 2)->default(21);
            $table->decimal('vat_amount', 12, 2);
            $table->decimal('total', 12, 2);

            // Terms and conditions (hardcoded)
            $table->text('terms_conditions')->nullable();
            $table->text('exclusions')->nullable();

            // Metadata
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoices');
    }
};
