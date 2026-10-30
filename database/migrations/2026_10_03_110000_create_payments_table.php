<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->date('payment_date');

            // Online gateway (SSLCommerz) tracking - NULL for manual payments
            $table->string('gateway', 30)->nullable();
            $table->string('transaction_id', 100)->nullable()->unique();
            $table->string('gateway_val_id', 100)->nullable();

            // Success | Pending | Failed | Cancelled  (only Success counts toward the invoice)
            $table->string('status', 20)->default('Success');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Keep money that was already recorded in invoices.paid_amount
        $now = now();
        foreach (DB::table('invoices')->where('paid_amount', '>', 0)->get() as $invoice) {
            DB::table('payments')->insert([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->paid_amount,
                'payment_method' => 'Previous Record',
                'payment_date' => $invoice->invoice_date,
                'status' => 'Success',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
