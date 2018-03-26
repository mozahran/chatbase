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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::FIELD_CONVERSATION_ID,
        self::FIELD_USER_ID,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        self::FIELD_CONVERSATION_ID => 'integer',
        self::FIELD_USER_ID => 'integer',
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
     * Get the conversation Id.
     *
     * @return int
     */
    public function getConversationId()
    {
        return (int) $this->getAttribute(self::FIELD_CONVERSATION_ID);
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

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    /**
     * Set the conversation Id.
     *
     * @param int $id
     * @return $this
     */
    public function setConversationId(int $id)
    {
        return $this->setAttribute(self::FIELD_CONVERSATION_ID, $id);
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

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    /**
     * The reply that the user is allowed to see.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conversation()
    {
        return $this->belongsTo(
            Conversation::class,
            Conversation::FIELD_PK,
            self::FIELD_CONVERSATION_ID
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
            self::FIELD_USER_ID,
            User::FIELD_PK
        )->with('image');
    }
}
