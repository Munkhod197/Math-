<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toy_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('level')->default(1);
            $table->unsignedInteger('xp')->default(0);
            $table->unsignedInteger('xp_next')->default(100);
            $table->unsignedInteger('coins')->default(0);
            $table->unsignedTinyInteger('accuracy')->default(0); // 0-100
            $table->unsignedInteger('streak')->default(0);
            $table->unsignedInteger('games_completed')->default(0);
            $table->string('rank', 10)->default('B3'); // B3, B2, B1, A3... S
            $table->unsignedInteger('study_seconds')->default(0);
            $table->date('last_played_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toy_profiles');
    }
};
