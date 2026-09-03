<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toy_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('key', 50);           // diamond_scholar, speed_demon, perfect_streak...
            $table->string('title');
            $table->string('icon', 10)->nullable();
            $table->string('tier', 20)->default('Bronze'); // Bronze, Silver, Gold, Crystal, Legendary
            $table->unsignedTinyInteger('progress')->default(0); // 0-100
            $table->boolean('unlocked')->default(false);
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toy_achievements');
    }
};
