<?php

use App\ConversationReply;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ConversationReply::TABLE_NAME, function (Blueprint $table) {
            $table->increments(ConversationReply::FIELD_PK);
            $table->unsignedInteger(ConversationReply::FIELD_CONVERSATION_ID);
            $table->unsignedInteger(ConversationReply::FIELD_SENDER_ID);
            $table->text(ConversationReply::FIELD_TEXT);
            $table->timestamps();
//            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ConversationReply::TABLE_NAME);
    }
}
