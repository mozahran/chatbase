<?php

use App\Chat;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Chat::TABLE_NAME, function (Blueprint $table) {
            $table->increments(Chat::FIELD_PK);
            $table->unsignedInteger(Chat::FIELD_CREATOR_ID);
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
        Schema::dropIfExists(Chat::TABLE_NAME);
    }
}
