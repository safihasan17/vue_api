<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * admission_id = NULL  -> patient was NOT admitted (tests done, went home)
     * admission_id = <id>  -> patient was admitted from this prescription
     */
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('admission_id')
                ->nullable()
                ->after('appointment_id')
                ->constrained('admissions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admission_id');
        });
    }
};
