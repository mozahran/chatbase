<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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

    public function setConversationId(int $id) : self
    {
        return $this->setAttribute(self::FIELD_CONVERSATION_ID, $id);
    }

    public function setSenderId(int $id) : self
    {
        return $this->setAttribute(self::FIELD_SENDER_ID, $id);
    }

    public function setText(string $text) : self
    {
        return $this->setAttribute(self::FIELD_TEXT, $text);
    }

    // ----------------------------------------------------------------------
    // Scope
    // ----------------------------------------------------------------------

    public function scopeOfConversation(Builder $query, int $id) : Builder
    {
        return $query->where(self::FIELD_CONVERSATION_ID, $id);
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