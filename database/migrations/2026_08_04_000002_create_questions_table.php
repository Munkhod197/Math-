<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('topic_id');
            $table->text('question_text');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->char('correct_answer', 1);
            $table->text('explanation')->nullable();
            $table->timestamps();

            $table->foreign('topic_id')->references('id')->on('topics')->cascadeOnDelete();
            $table->index('topic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
