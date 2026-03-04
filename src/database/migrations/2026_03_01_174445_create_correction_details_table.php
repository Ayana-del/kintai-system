<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correction_request_id')->constrained()->onDelete('cascade');
            $table->string('type', 50);
            $table->time('modified_time');
            $table->unsignedBigInteger('rest_id')->nullable();
            $table->timestamps();

            $table->foreign('rest_id')->references('id')->on('rests')->onDelete('cascade');

            $table->unique(['correction_request_id', 'type', 'rest_id'], 'unique_detail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correction_details');
    }
}
