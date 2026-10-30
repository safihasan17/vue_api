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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();

            // Nullable — only set when the invoice belongs to an admitted (IPD) patient
            $table->foreignId('admission_id')->nullable()->constrained('admissions')->nullOnDelete();

            // Nullable — only set when the invoice belongs to an OPD appointment
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();

            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('status', 20)->default('Unpaid');
            $table->date('invoice_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};