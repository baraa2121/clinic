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
        Schema::create('payments', function (Blueprint $table) {
    $table->id();

    // مربوط بالحجز
    $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();

    // المريض (اختياري بس مفيد)
    $table->foreignId('patient_id')->constrained()->cascadeOnDelete();

    // المبلغ
    $table->decimal('amount', 8, 2);

    // طريقة الدفع
    $table->enum('method', ['cash','card','online'])->default('cash');

    // حالة الدفع
    $table->enum('status', ['pending','paid','failed'])->default('pending');

    // رقم العملية (لـ online)
    $table->string('transaction_id')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
