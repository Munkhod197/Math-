use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. topics болон questions хүснэгтүүдийн холбоосыг зөв болгохын тулд
        // Хэрэв table бүтцэд өөрчлөлт орох гэж байгаа бол foreign key-г аюулгүйгээр устгана
        Schema::table('questions', function (Blueprint $table) {
            // Хэрэв constraint байгаа бол drop хийх
            try {
                $table->dropForeign(['topic_id']);
            } catch (\Exception $e) {
                // байхгүй бол алдааг алгасна
            }
        });

        // 2. topic_id болон topics.id төрлийг бүрэн тааруулах
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('topic_id')->change();
        });

        // 3. Foreign key-г дахин холбох
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
