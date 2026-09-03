<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
        });

        DB::statement('ALTER TABLE `topics` MODIFY `id` INT NOT NULL AUTO_INCREMENT;');

        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('topic_id')->references('id')->on('topics')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
        });

        DB::statement('ALTER TABLE `topics` MODIFY `id` INT NOT NULL;');

        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('topic_id')->references('id')->on('topics')->cascadeOnDelete();
        });
    }
};
