<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ChatReply extends Model
{
    // ----------------------------------------------------------------------
    // Table Schema
    // ----------------------------------------------------------------------

    const TABLE_NAME = 'chat_replies';

    const FIELD_PK = 'id';
    const FIELD_CHAT_ID = 'chat_id';
    const FIELD_SENDER_ID = 'sender_id';
    const FIELD_TEXT = 'text';

    protected $fillable = [
        self::FIELD_CHAT_ID,
        self::FIELD_SENDER_ID,
        self::FIELD_TEXT,
    ];

    protected $casts = [
        self::FIELD_CHAT_ID   => 'integer',
        self::FIELD_SENDER_ID => 'integer',
    ];

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    public function getId() : ?int
    {
        return $this->getAttribute(self::FIELD_PK);
    }

    public function getConversationId() :? int
    {
        return $this->getAttribute(self::FIELD_CHAT_ID);
    }

    public function getSenderId() :? int
    {
        return $this->getAttribute(self::FIELD_SENDER_ID);
    }

    public function getText() : ?string
    {
        return $this->getAttribute(self::FIELD_TEXT);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    public function setChat(Chat $chat) : self
    {
        return $this->setAttribute(self::FIELD_CHAT_ID, $chat->getId());
    }

    public function setSender(User $sender) : self
    {
        return $this->setAttribute(self::FIELD_SENDER_ID, $sender->getId());
    }

    public function setText(string $text) : self
    {
        return $this->setAttribute(self::FIELD_TEXT, $text);
    }

    // ----------------------------------------------------------------------
    // Scope
    // ----------------------------------------------------------------------

    public function scopeOfConversation(Builder $query, Chat $chat) : Builder
    {
        return $query->where(self::FIELD_CHAT_ID, $chat->getId());
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    public function chat() : ?\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            Chat::class,
            self::FIELD_CHAT_ID,
            Chat::FIELD_PK
        );
    }

    public function sender() : ?\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::FIELD_SENDER_ID,
            User::FIELD_PK
        );
    }

    public function recipients() : ?\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            ChatReplyUser::class,
            ChatReplyUser::FIELD_CHAT_REPLY_ID,
            self::FIELD_PK
        );
    }
}
