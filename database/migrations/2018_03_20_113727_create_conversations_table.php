<?php

use App\Conversation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Conversation::TABLE_NAME, function (Blueprint $table) {
            $table->increments(Conversation::FIELD_PK);
            $table->unsignedInteger(Conversation::FIELD_CREATOR_ID);
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
        Schema::dropIfExists(Conversation::TABLE_NAME);
    }
}
