<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedirectsTable extends Migration
{
    public function up()
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_slug', 255);
            $table->string('new_slug', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('redirects');
    }
}
