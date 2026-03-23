<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'client'])->default('client')->after('password');
            $table->boolean('is_active')->default(false)->after('role');
            $table->string('company_name')->nullable()->after('is_active');
            $table->string('inn')->nullable()->after('company_name');
            $table->string('mfo')->nullable()->after('inn');
            $table->string('bank_account')->nullable()->after('mfo');
            $table->string('bank_name')->nullable()->after('bank_account');
            $table->string('director_name')->nullable()->after('bank_name');
            $table->string('phone')->nullable()->after('director_name');
            $table->string('address')->nullable()->after('phone');
            $table->string('logo')->nullable()->after('address');
            $table->boolean('contract_agreed')->default(false)->after('logo');
            $table->timestamp('approved_at')->nullable()->after('contract_agreed');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['role', 'is_active', 'company_name', 'inn', 'mfo', 'bank_account', 'bank_name', 'director_name', 'phone', 'address', 'logo', 'contract_agreed', 'approved_at', 'approved_by']);
        });
    }
};
