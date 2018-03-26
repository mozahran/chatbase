<?php

use App\ConversationUser;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ConversationUser::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedInteger(ConversationUser::FIELD_CONVERSATION_ID);
            $table->unsignedInteger(ConversationUser::FIELD_USER_ID);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ConversationUser::TABLE_NAME);
    }
}
