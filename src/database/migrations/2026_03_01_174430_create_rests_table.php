<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rests', function (Blueprint $table) {
            $table->id(); // 1. 休憩ID
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade'); // 2. 勤怠ID
            $table->time('rest_start'); // 3. 休憩開始時刻
            $table->time('rest_end')->nullable(); // 4. 休憩終了時刻
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rests');
    }
}
