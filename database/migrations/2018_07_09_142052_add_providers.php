<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (env('APP_ENV') !== 'production') {
            Schema::dropIfExists('providers');

            Schema::create('providers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('_id')->unique();
                $table->string('name');
                $table->timestamps();
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('updated_by');
                $table->unsignedBigInteger('deleted_by');
                $table->softDeletes();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('providers');
    }
}
