<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ConversationReply extends Model
{
    // ----------------------------------------------------------------------
    // Table Schema
    // ----------------------------------------------------------------------

    const TABLE_NAME = 'conversation_replies';

    const FIELD_PK = 'id';
    const FIELD_CONVERSATION_ID = 'conversation_id';
    const FIELD_SENDER_ID = 'sender_id';
    const FIELD_TEXT = 'text';

    protected $fillable = [
        self::FIELD_CONVERSATION_ID,
        self::FIELD_SENDER_ID,
        self::FIELD_TEXT,
    ];

    protected $casts = [
        self::FIELD_CONVERSATION_ID => 'integer',
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
        return $this->getAttribute(self::FIELD_CONVERSATION_ID);
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

    public function setConversation(Conversation $conversation) : self
    {
        return $this->setAttribute(self::FIELD_CONVERSATION_ID, $conversation->getId());
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

    public function scopeOfConversation(Builder $query, Conversation $conversation) : Builder
    {
        return $query->where(self::FIELD_CONVERSATION_ID, $conversation->getId());
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    public function conversation() : ?\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            Conversation::class,
            self::FIELD_CONVERSATION_ID,
            Conversation::FIELD_PK
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
            ConversationReplyUser::class,
            ConversationReplyUser::FIELD_CONVERSATION_REPLY_ID,
            self::FIELD_PK
        );
    }
}