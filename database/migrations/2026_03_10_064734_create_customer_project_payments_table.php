<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_project_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_project_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('note')->nullable();
            $table->foreignId('received_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['customer_project_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_project_payments');
    }
};