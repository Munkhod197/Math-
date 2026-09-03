<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Foreign key байгаа эсэхийг шалгаад устгана
        $foreignKey = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'questions'
              AND COLUMN_NAME = 'topic_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ");

        if ($foreignKey) {
            Schema::table('questions', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            });
        }

        // 2. topic_id-г topics.id-тай ижил төрөл болгоно
        DB::statement('ALTER TABLE questions MODIFY topic_id BIGINT UNSIGNED NOT NULL');

        // 3. Foreign key-г дахин нэмнэ
        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('topic_id')
                  ->references('id')
                  ->on('topics')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        //
    }
};
