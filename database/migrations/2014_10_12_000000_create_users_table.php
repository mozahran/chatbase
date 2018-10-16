<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(User::TABLE_NAME, function (Blueprint $table) {
            $table->increments(User::FIELD_PK);
            $table->string(User::FIELD_NAME);
            $table->string(User::FIELD_EMAIL)->unique();
            $table->string(User::FIELD_PASSWORD);
            $table->rememberToken();
            $table->boolean(User::FIELD_IS_ACTIVE)->default(User::STATUS_INACTIVE);
            $table->boolean(User::FIELD_IS_SUSPENDED)->default(User::STATUS_NOT_SUSPENDED);
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
        Schema::dropIfExists(User::TABLE_NAME);
    }
}
