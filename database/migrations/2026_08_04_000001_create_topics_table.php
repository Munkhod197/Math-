<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('icon', 10)->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('grade_level');
            $table->string('video_url')->nullable();
            $table->string('video_title')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
