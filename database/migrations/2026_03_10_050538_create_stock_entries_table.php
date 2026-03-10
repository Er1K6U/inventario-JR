<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reason');
            $table->timestamp('entered_at');
            $table->timestamps();

            $table->index(['entered_at']);
            $table->index(['product_id', 'entered_at']);
            $table->index(['user_id', 'entered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_entries');
    }
};