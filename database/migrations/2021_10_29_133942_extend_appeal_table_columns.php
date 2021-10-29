<?php

use App\Enums\Gender;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendAppealTableColumns extends Migration
{
    public function up()
    {
        Schema::table('appeals', function (Blueprint $table) {
            $table->string('surname', 40);
            $table->string('patronymic', 20)->nullable();
            $table->integer('age');
            $table->enum('gender', [Gender::MALE, Gender::FEMALE]);
        });
    }

    public function down()
    {
        Schema::table('appeals', function (Blueprint $table) {
            $table->dropColumn('surname');
            $table->dropColumn('patronymic');
            $table->dropColumn('age');
            $table->dropColumn('gender');
        });
    }
}
