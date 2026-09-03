<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toy_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('mode', 50);          // quick-play, speedrun, algebra, geometry...
            $table->string('difficulty', 20)->default('normal'); // easy, normal, hard
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('max_combo')->default(0);
            $table->unsignedTinyInteger('accuracy')->default(0);
            $table->unsignedInteger('rounds_played')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->timestamps();

            $table->index(['mode', 'score']);          // leaderboard-д
            $table->index(['user_id', 'mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toy_scores');
    }
};
