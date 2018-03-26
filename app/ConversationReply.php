<?php

namespace App;

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::FIELD_CONVERSATION_ID,
        self::FIELD_SENDER_ID,
        self::FIELD_TEXT,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        self::FIELD_CONVERSATION_ID => 'integer',
        self::FIELD_SENDER_ID => 'integer',
    ];

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    /**
     * Get the reply Id.
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->getAttribute(self::FIELD_PK);
    }

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
     * Get the sender Id.
     *
     * @return int
     */
    public function getSenderId()
    {
        return (int) $this->getAttribute(self::FIELD_SENDER_ID);
    }

    /**
     * Get the reply text.
     *
     * @return mixed
     */
    public function getText()
    {
        return $this->getAttribute(self::FIELD_TEXT);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    /**
     * Set the conversation Id.
     *
     * @param int $id
     * @return ConversationReply
     */
    public function setConversationId(int $id)
    {
        return $this->setAttribute(self::FIELD_CONVERSATION_ID, $id);
    }

    /**
     * Set the sender Id.
     *
     * @param int $id
     * @return ConversationReply
     */
    public function setSenderId(int $id)
    {
        return $this->setAttribute(self::FIELD_SENDER_ID, $id);
    }

    /**
     * Set the reply text.
     *
     * @param string $text
     * @return mixed
     */
    public function setText(string $text)
    {
        return $this->setAttribute(self::FIELD_TEXT, $text);
    }

    // ----------------------------------------------------------------------
    // Scope
    // ----------------------------------------------------------------------

    /**
     * Scope replies by conversation Id.
     *
     * @param $query
     * @param int $id
     * @return mixed
     */
    public function scopeOfConversation($query, int $id)
    {
        return $query->where(self::FIELD_CONVERSATION_ID, $id);
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    /**
     * The conversation that the reply belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conversation()
    {
        return $this->belongsTo(
            Conversation::class,
            self::FIELD_CONVERSATION_ID,
            Conversation::FIELD_PK
        );
    }

    /**
     * The user that the reply belongs to.
     * (the sender of the reply)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(
            User::class,
            self::FIELD_SENDER_ID,
            User::FIELD_PK
        );
    }

    /**
     * The recipients of the reply.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipients()
    {
        return $this->hasMany(
            ConversationReplyUser::class,
            ConversationReplyUser::FIELD_CONVERSATION_REPLY_ID,
            self::FIELD_PK
        );
    }
}
