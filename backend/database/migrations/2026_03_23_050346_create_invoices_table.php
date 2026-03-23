<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('booking_id')->constrained();
            $table->foreignId('client_id')->constrained('users');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2);
            $table->date('due_date')->nullable();
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
