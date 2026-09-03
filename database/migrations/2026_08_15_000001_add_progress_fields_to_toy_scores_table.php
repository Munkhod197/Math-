<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('toy_scores', function (Blueprint $table) {
            $table->unsignedSmallInteger('correct_answers')->default(0)->after('score');
            $table->unsignedSmallInteger('wrong_answers')->default(0)->after('correct_answers');
            $table->unsignedInteger('xp_earned')->default(0)->after('duration_seconds');
            $table->unsignedInteger('coins_earned')->default(0)->after('xp_earned');
        });
    }

    public function down(): void
    {
        Schema::table('toy_scores', function (Blueprint $table) {
            $table->dropColumn(['correct_answers', 'wrong_answers', 'xp_earned', 'coins_earned']);
        });
    }
};
