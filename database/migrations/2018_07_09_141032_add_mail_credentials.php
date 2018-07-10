<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMailCredentials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (env('APP_ENV') !== 'production') {
            Schema::dropIfExists('credentials');

            Schema::create('credentials', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('_id')->unique();
                $table->string('name');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('provider_id');
                $table->string('username');
                $table->string('secret');
                $table->string('domain');
                $table->string('mail_from_address');
                $table->string('mail_from_name');
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
        Schema::dropIfExists('credentials');
    }
}
