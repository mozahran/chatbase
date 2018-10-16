<?php

use App\ChatReply;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ChatReply::TABLE_NAME, function (Blueprint $table) {
            $table->increments(ChatReply::FIELD_PK);
            $table->unsignedInteger(ChatReply::FIELD_CHAT_ID);
            $table->unsignedInteger(ChatReply::FIELD_SENDER_ID);
            $table->text(ChatReply::FIELD_TEXT);
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
        Schema::dropIfExists(ChatReply::TABLE_NAME);
    }
}
