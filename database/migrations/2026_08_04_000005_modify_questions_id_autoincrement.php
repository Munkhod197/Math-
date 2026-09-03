<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `questions` MODIFY `id` INT NOT NULL AUTO_INCREMENT;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `questions` MODIFY `id` INT NOT NULL;');
    }
};
