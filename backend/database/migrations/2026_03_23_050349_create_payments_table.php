<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['invoice_payment', 'deposit']);
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('bank_transfer');
            $table->text('notes')->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
