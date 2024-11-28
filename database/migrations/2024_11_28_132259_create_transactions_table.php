<?php

use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id')->index();
            $table->string('bill_id')->index();
            $table->float('amount');
            $table->string('transaction_type');
            $table->string('transaction_status')->default(TransactionStatus::Pending->value);
            $table->string('transaction_reference');
            $table->timestamps();
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