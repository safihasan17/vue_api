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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();

            // item_type + item_reference_id together point to a row in a
            // different table depending on the type (Medicine, LabTest, etc).
            // Since the referenced table varies, this is NOT a real foreign
            // key — no constrained()/cascadeOnDelete() is possible here.
            $table->string('item_type', 50);
            $table->unsignedBigInteger('item_reference_id')->nullable();

            $table->string('description', 255);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};