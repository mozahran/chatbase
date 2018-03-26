<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    // ----------------------------------------------------------------------
    // Table Schema
    // ----------------------------------------------------------------------

    const TABLE_NAME = 'conversations';

    const FIELD_PK = 'id';
    const FIELD_CREATOR_ID = 'creator_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::FIELD_CREATOR_ID,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        self::FIELD_CREATOR_ID => 'integer',
    ];

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    /**
     * Get the conversation Id.
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->getAttribute(self::FIELD_PK);
    }

    /**
     * Get the Id of the conversation creator.
     *
     * @return int
     */
    public function getCreatorId()
    {
        return (int) $this->getAttribute(self::FIELD_CREATOR_ID);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    /**
     * Set the Id of the conversation creator.
     *
     * @param int $id
     * @return $this
     */
    public function setCreatorId(int $id)
    {
        return $this->setAttribute(self::FIELD_CREATOR_ID, $id);
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    /**
     * The user that the conversation belongs to.
     * (the creator of the conversation)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            User::FIELD_PK,
            self::FIELD_CREATOR_ID
        );
    }

    /**
     * The replies that belongs to the conversation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(
            ConversationReply::class,
            ConversationReply::FIELD_CONVERSATION_ID,
            self::FIELD_PK
        );
    }

    /**
     * The last reply in the conversation.
     *
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function lastReply()
    {
        return $this->hasOne(
            ConversationReply::class,
            ConversationReply::FIELD_CONVERSATION_ID,
            self::FIELD_PK
        )->latest('created_at');
    }

    /**
     * The users that are involved in the conversation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(
            ConversationUser::class,
            ConversationUser::FIELD_CONVERSATION_ID,
            self::FIELD_PK
        )->with('user.image');
    }
}
