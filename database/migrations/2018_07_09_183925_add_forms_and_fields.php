<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormsAndFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (env('APP_ENV') !== 'production') {
            Schema::dropIfExists('forms');
            Schema::dropIfExists('fields');
        }

        Schema::create('forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('_id')->unique();
            $table->string('name');
            $table->text('response_template');
            $table->unsignedBigInteger('credentials_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('deleted_by');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('_id')->unique();
            $table->string('name');
            $table->string('label');
            $table->string('type')->default('text');
            $table->unsignedInteger('min_length');
            $table->unsignedInteger('max_length');
            $table->string('regex')->nullable();
            $table->boolean('required')->default(false);
            $table->unsignedBigInteger('form_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('deleted_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
        Schema::dropIfExists('fields');
    }
}
