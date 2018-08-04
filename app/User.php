<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    // ----------------------------------------------------------------------
    // Table Schema
    // ----------------------------------------------------------------------

    const TABLE_NAME = 'users';
    const FIELD_PK = 'id';
    const FIELD_NAME = 'name';
    const FIELD_EMAIL = 'email';
    const FIELD_PASSWORD = 'password';
    const FIELD_REMEMBER_TOKEN = 'remember_token';
    const FIELD_IS_ACTIVE = 'is_active';
    const FIELD_IS_SUSPENDED = 'is_suspended';

    const STATUS_ALL = -1;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS_SUSPENDED = 1;
    const STATUS_NOT_SUSPENDED = 0;

    protected $fillable = [
        self::FIELD_NAME,
        self::FIELD_EMAIL,
        self::FIELD_PASSWORD,
        self::FIELD_IS_ACTIVE,
        self::FIELD_IS_SUSPENDED,
    ];

    protected $casts = [
        self::FIELD_IS_SUSPENDED => 'boolean',
        self::FIELD_IS_ACTIVE => 'boolean',
    ];

    protected $hidden = [
        self::FIELD_PASSWORD,
        self::FIELD_REMEMBER_TOKEN,
    ];

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    public function getId() : ?int
    {
        return $this->getAttribute(self::FIELD_PK);
    }

    public function getName() : ?string
    {
        return $this->getAttribute(self::FIELD_NAME);
    }

    public function getEmail() : ?string
    {
        return $this->getAttribute(self::FIELD_EMAIL);
    }

    public function isActive() : ?bool
    {
        return $this->getAttribute(self::FIELD_IS_ACTIVE);
    }

    public function isSuspended() : ?bool
    {
        return $this->getAttribute(self::FIELD_IS_SUSPENDED);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    public function setName(string $name) : self
    {
        return $this->setAttribute(self::FIELD_NAME, $name);
    }

    public function setEmail(string $email) : self
    {
        return $this->setAttribute(self::FIELD_EMAIL, $email);
    }

    public function setPassword($value) : self
    {
        return $this->setAttribute(self::FIELD_PASSWORD, bcrypt($value));
    }

    public function setActive() : self
    {
        return $this->setAttribute(self::FIELD_IS_ACTIVE, true);
    }

    public function setInactive() : self
    {
        return $this->setAttribute(self::FIELD_IS_ACTIVE, false);
    }

    public function setSuspended() : self
    {
        return $this->setAttribute(self::FIELD_IS_SUSPENDED, true);
    }

    public function setNotSuspended() : self
    {
        return $this->setAttribute(self::FIELD_IS_SUSPENDED, false);
    }

    // ----------------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------------

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->where(self::FIELD_IS_ACTIVE, true);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeInactive(Builder $query) : Builder
    {
        return $query->where(self::FIELD_IS_ACTIVE, false);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeSuspended(Builder $query) : Builder
    {
        return $query->where(self::FIELD_IS_SUSPENDED, true);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNotSuspended(Builder $query) : Builder
    {
        return $query->where(self::FIELD_IS_SUSPENDED, false);
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    public function conversations() : \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Conversation::class,
            ConversationUser::class,
            ConversationUser::FIELD_USER_ID,
            self::FIELD_PK,
            Conversation::FIELD_PK,
            ConversationUser::FIELD_CONVERSATION_ID
        );
    }
}
