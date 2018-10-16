<?php

use App\ChatReplyUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatReplyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ChatReplyUser::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedInteger(ChatReplyUser::FIELD_CHAT_REPLY_ID);
            $table->unsignedInteger(ChatReplyUser::FIELD_USER_ID);
            $table->dateTime(ChatReplyUser::FIELD_SEEN_AT)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ChatReplyUser::TABLE_NAME);
    }
}
