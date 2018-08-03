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

    protected $fillable = [
        self::FIELD_CREATOR_ID,
    ];

    protected $casts = [
        self::FIELD_CREATOR_ID => 'integer',
    ];

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    public function getId() : ?int
    {
        return $this->getAttribute(self::FIELD_PK);
    }

    public function getCreatorId() : ?int
    {
        return $this->getAttribute(self::FIELD_CREATOR_ID);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    public function setCreator(User $user) : self
    {
        return $this->setAttribute(self::FIELD_CREATOR_ID, $user->getId());
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    public function creator() : ?\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::FIELD_CREATOR_ID,
            User::FIELD_PK
        );
    }

    public function replies() : ?\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            ConversationReply::class,
            ConversationReply::FIELD_CONVERSATION_ID,
            self::FIELD_PK
        );
    }

    public function lastReply() : ?\Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(
            ConversationReply::class,
            ConversationReply::FIELD_CONVERSATION_ID,
            self::FIELD_PK
        )->latest('created_at');
    }

    public function users() : ?\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            ConversationUser::class,
            ConversationUser::FIELD_CONVERSATION_ID,
            self::FIELD_PK
        );
    }
}