<?php

use App\ChatUser;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ChatUser::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedInteger(ChatUser::FIELD_CHAT_ID);
            $table->unsignedInteger(ChatUser::FIELD_USER_ID);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ChatUser::TABLE_NAME);
    }
}
