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
        Schema::table('users', function (Blueprint $table) {
            $table->string('billing_plan')->nullable()->after('password');
            $table->string('billing_status')->default('free')->after('billing_plan');
            $table->timestamp('billing_ends_at')->nullable()->after('billing_status');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('plan');
            $table->integer('amount');
            $table->string('currency')->default('MNT');
            $table->string('status')->default('paid');
            $table->text('description')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['billing_plan', 'billing_status', 'billing_ends_at']);
        });
    }
};
