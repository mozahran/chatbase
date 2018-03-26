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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::FIELD_CONVERSATION_REPLY_ID,
        self::FIELD_USER_ID,
        self::FIELD_SEEN_AT,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        self::FIELD_CONVERSATION_REPLY_ID => 'integer',
        self::FIELD_USER_ID => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        self::FIELD_SEEN_AT,
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    /**
     * Get the conversation reply Id.
     *
     * @return int
     */
    public function getConversationReplyId()
    {
        return (int) $this->getAttribute(self::FIELD_CONVERSATION_REPLY_ID);
    }

    /**
     * Get the user Id.
     *
     * @return int
     */
    public function getUserId()
    {
        return (int) $this->getAttribute(self::FIELD_USER_ID);
    }

    /**
     * Get the time of seeing the reply.
     *
     * @return mixed
     */
    public function getSeenAt()
    {
        return $this->getAttribute(self::FIELD_SEEN_AT);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    /**
     * Set the conversation reply Id.
     *
     * @param int $id
     * @return $this
     */
    public function setConversationReplyId(int $id)
    {
        return $this->setAttribute(self::FIELD_CONVERSATION_REPLY_ID, $id);
    }

    /**
     * Set the user Id.
     *
     * @param int $id
     * @return $this
     */
    public function setUserId(int $id)
    {
        return $this->setAttribute(self::FIELD_USER_ID, $id);
    }

    /**
     * Set the time of seeing the reply.
     *
     * @param Carbon $seenAt
     * @return $this
     */
    public function setSeenAt(Carbon $seenAt)
    {
        return $this->setAttribute(self::FIELD_SEEN_AT, $seenAt);
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    /**
     * The reply that the user is allowed to see.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reply()
    {
        return $this->belongsTo(
            ConversationReply::class,
            ConversationReply::FIELD_PK,
            self::FIELD_CONVERSATION_REPLY_ID
        );
    }

    /**
     * The user that is allowed to see the reply.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            User::FIELD_PK,
            self::FIELD_USER_ID
        );
    }
}
