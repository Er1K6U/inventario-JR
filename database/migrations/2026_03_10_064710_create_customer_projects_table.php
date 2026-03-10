<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('project_date');
            $table->text('note')->nullable();
            $table->enum('status', ['abierto', 'cerrado', 'anulado'])->default('abierto');

            $table->foreignId('opened_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['project_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_projects');
    }
};