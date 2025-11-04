<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedule_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Внешний ключ к таблице users
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('day_of_week'); // 1 = Пн, 7 = Вс
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        // Добавляем ограничения (check constraint) после создания таблицы
        DB::statement('ALTER TABLE schedule_entries ADD CONSTRAINT check_end_time_gt_start_time CHECK (end_time > start_time)');
        DB::statement('ALTER TABLE schedule_entries ADD CONSTRAINT check_day_of_week_range CHECK (day_of_week BETWEEN 1 AND 7)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_entries');
    }
};
