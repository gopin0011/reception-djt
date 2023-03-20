<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('tamu');
            $table->string('asal')->nullable();
            $table->string('bertemu')->nullable();
            $table->string('tujuan')->nullable();
            $table->string('datang')->nullable();
            $table->string('pulang')->nullable();
            $table->string('suhu')->nullable();
            $table->string('gerbang')->nullable();
            $table->string('ruangan')->nullable();
            $table->string('sekuriti')->nullable();
            $table->string('acc')->nullable();
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
        Schema::dropIfExists('guests');
    }
};
