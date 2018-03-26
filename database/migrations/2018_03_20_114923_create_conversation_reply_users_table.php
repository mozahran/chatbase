<?php

use App\ConversationReplyUser;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationReplyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ConversationReplyUser::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedInteger(ConversationReplyUser::FIELD_CONVERSATION_REPLY_ID);
            $table->unsignedInteger(ConversationReplyUser::FIELD_USER_ID);
            $table->dateTime(ConversationReplyUser::FIELD_SEEN_AT)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ConversationReplyUser::TABLE_NAME);
    }
}
