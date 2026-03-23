<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('menu_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('name_uz')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_ru')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('unit')->default('portion');
            $table->string('image')->nullable();
            $table->integer('min_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
