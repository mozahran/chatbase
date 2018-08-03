<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ConversationReplyUser extends Pivot
{
    // ----------------------------------------------------------------------
    // Table Schema
    // ----------------------------------------------------------------------

    const TABLE_NAME = 'conversation_reply_user';

    const FIELD_CONVERSATION_REPLY_ID = 'conversation_reply_id';
    const FIELD_USER_ID = 'user_id';
    const FIELD_SEEN_AT = 'seen_at';

    protected $fillable = [
        self::FIELD_CONVERSATION_REPLY_ID,
        self::FIELD_USER_ID,
        self::FIELD_SEEN_AT,
    ];

    protected $casts = [
        self::FIELD_CONVERSATION_REPLY_ID => 'integer',
        self::FIELD_USER_ID => 'integer',
    ];

    protected $dates = [
        self::FIELD_SEEN_AT,
    ];

    public $incrementing = false;
    public $timestamps = false;

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    public function getConversationReplyId() : ?int
    {
        return $this->getAttribute(self::FIELD_CONVERSATION_REPLY_ID);
    }

    public function getUserId() : ?int
    {
        return $this->getAttribute(self::FIELD_USER_ID);
    }

    public function getSeenAt() : ?Carbon
    {
        return $this->getAttribute(self::FIELD_SEEN_AT);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    public function setConversationReply(ConversationReply $reply) : self
    {
        return $this->setAttribute(self::FIELD_CONVERSATION_REPLY_ID, $reply->getId());
    }

    public function setUser(User $user) : self
    {
        return $this->setAttribute(self::FIELD_USER_ID, $user->getId());
    }

    public function setSeenAt(Carbon $seenAt) : self
    {
        return $this->setAttribute(self::FIELD_SEEN_AT, $seenAt);
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    public function reply() : ?\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            ConversationReply::class,
            ConversationReply::FIELD_PK,
            self::FIELD_CONVERSATION_REPLY_ID
        );
    }

    public function user() : ?\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            User::class,
            User::FIELD_PK,
            self::FIELD_USER_ID
        );
    }
}