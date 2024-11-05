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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->string('reference_number')->unique();
            $table->string('provider_transaction_id');
            $table->decimal('amount', 13, 4);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['provider_id', 'reference_number']); // Composite index to ensure only one unique transaction per provider
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
