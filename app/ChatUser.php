<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ChatUser extends Pivot
{
    // ----------------------------------------------------------------------
    // Table Schema
    // ----------------------------------------------------------------------

    const TABLE_NAME = 'chat_user';

    const FIELD_CHAT_ID = 'chat_id';
    const FIELD_USER_ID = 'user_id';

    protected $fillable = [
        self::FIELD_CHAT_ID,
        self::FIELD_USER_ID,
    ];

    protected $casts = [
        self::FIELD_CHAT_ID => 'integer',
        self::FIELD_USER_ID => 'integer',
    ];

    public $incrementing = false;
    public $timestamps = false;

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    public function getConversationId() : ?int
    {
        return $this->getAttribute(self::FIELD_CHAT_ID);
    }

    public function getUserId() : ?int
    {
        return $this->getAttribute(self::FIELD_USER_ID);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    public function setChat(Chat $chat) : self
    {
        return $this->setAttribute(self::FIELD_CHAT_ID, $chat->getId());
    }

    public function setUser(User $user) : self
    {
        return $this->setAttribute(self::FIELD_USER_ID, $user->getId());
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    public function chat() : ?\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            Chat::class,
            Chat::FIELD_PK,
            self::FIELD_CHAT_ID
        );
    }

    public function user() : ?\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::FIELD_USER_ID,
            User::FIELD_PK
        );
    }
}
