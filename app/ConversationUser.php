<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ConversationUser extends Pivot
{
    // ----------------------------------------------------------------------
    // Table Schema
    // ----------------------------------------------------------------------

    const TABLE_NAME = 'conversation_user';

    const FIELD_CONVERSATION_ID = 'conversation_id';
    const FIELD_USER_ID = 'user_id';

    protected $fillable = [
        self::FIELD_CONVERSATION_ID,
        self::FIELD_USER_ID,
    ];

    protected $casts = [
        self::FIELD_CONVERSATION_ID => 'integer',
        self::FIELD_USER_ID => 'integer',
    ];

    public $incrementing = false;
    public $timestamps = false;

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    public function getConversationId() : ?int
    {
        return $this->getAttribute(self::FIELD_CONVERSATION_ID);
    }

    public function getUserId() : ?int
    {
        return $this->getAttribute(self::FIELD_USER_ID);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    public function setConversation(Conversation $conversation) : self
    {
        return $this->setAttribute(self::FIELD_CONVERSATION_ID, $conversation->getId());
    }

    public function setUser(User $user) : self
    {
        return $this->setAttribute(self::FIELD_USER_ID, $user->getId());
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    public function conversation() : ?\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            Conversation::class,
            Conversation::FIELD_PK,
            self::FIELD_CONVERSATION_ID
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