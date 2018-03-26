<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        self::FIELD_PASSWORD,
        self::FIELD_REMEMBER_TOKEN,
    ];

    // ----------------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------------

    /**
     * Get the user Id.
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->getAttribute(self::FIELD_PK);
    }

    /**
     * Get the name of the user.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->getAttribute(self::FIELD_NAME);
    }

    /**
     * Get the email of the user.
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->getAttribute(self::FIELD_EMAIL);
    }

    /**
     * Checks if the user is active.
     *
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->getAttribute(self::FIELD_IS_ACTIVE);
    }

    /**
     * Checks if the user is active.
     *
     * @return bool
     */
    public function isSuspended()
    {
        return (bool) $this->getAttribute(self::FIELD_IS_SUSPENDED);
    }

    // ----------------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------------

    /**
     * Set the name of the user.
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        return $this->setAttribute(self::FIELD_NAME, $name);
    }

    /**
     * Set the email of the user.
     *
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email)
    {
        return $this->setAttribute(self::FIELD_EMAIL, $email);
    }

    /**
     * Set the password of the user.
     *
     * @param $value
     * @return string
     */
    public function setPassword($value)
    {
        return $this->setAttribute(self::FIELD_PASSWORD, bcrypt($value));
    }

    /**
     * Set the user as active.
     *
     * @return $this
     */
    public function setActive()
    {
        return $this->setAttribute(self::FIELD_IS_ACTIVE, true);
    }

    /**
     * Set the user as inactive.
     *
     * @return $this
     */
    public function setInactive()
    {
        return $this->setAttribute(self::FIELD_IS_ACTIVE, false);
    }

    /**
     * Set the user as suspended.
     *
     * @return $this
     */
    public function setSuspended()
    {
        return $this->setAttribute(self::FIELD_IS_SUSPENDED, true);
    }

    /**
     * Set the user as not-suspended.
     *
     * @return $this
     */
    public function setNotSuspended()
    {
        return $this->setAttribute(self::FIELD_IS_SUSPENDED, false);
    }

    // ----------------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------------

    /**
     * Scope active users.
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where(self::FIELD_IS_ACTIVE, true);
    }

    /**
     * Scope inactive users.
     *
     * @param $query
     * @return mixed
     */
    public function scopeInactive($query)
    {
        return $query->where(self::FIELD_IS_ACTIVE, false);
    }

    /**
     * Scope suspended users.
     *
     * @param $query
     * @return mixed
     */
    public function scopeSuspended($query)
    {
        return $query->where(self::FIELD_IS_SUSPENDED, true);
    }

    /**
     * Scope users that are not suspended.
     *
     * @param $query
     * @return mixed
     */
    public function scopeNotSuspended($query)
    {
        return $query->where(self::FIELD_IS_SUSPENDED, false);
    }

    // ----------------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------------

    /**
     * The conversations that the user is involved in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function conversations()
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
