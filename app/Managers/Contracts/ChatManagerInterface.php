<?php

namespace App\Managers\Contracts;

use App\Chat;
use App\ChatReply;
use App\User;
use Carbon\Carbon;

interface ChatManagerInterface
{
    /**
     * Create a new chat.
     *
     * @param User  $user
     * @param array $users
     *
     * @return Chat|null
     */
    public function createChat(User $user, array $users) : ?Chat;

    /**
     * Create a new reply.
     *
     * @param Chat        $chat
     * @param User        $sender
     * @param string      $text
     * @param Carbon|null $createdAt
     *
     * @return ChatReply|null
     */
    public function createReply(
        Chat $chat,
        User $sender,
        string $text,
        Carbon $createdAt = null
    ) : ?ChatReply;

    /**
     * Add user to an existing chat.
     *
     * @param User $user
     * @param Chat $chat
     *
     * @return bool
     */
    public function addUserToChat(User $user, Chat $chat) : bool;

    /**
     * Delete an existing chat.
     *
     * @param Chat $chat
     * @param User $user
     *
     * @return bool
     */
    public function deleteChat(Chat $chat, User $user) : bool;

    /**
     * Smart delete a reply.
     *
     * This method deletes the relation of the user (ChatReplyUser)
     * to the actual reply (ChatReply). If other users are no longer involved in
     * this reply (have no ChatRelyUser relations), the actual reply gets deleted as well.
     *
     * @param ChatReply $reply
     * @param User      $user
     *
     * @return bool
     */
    public function deleteReply(ChatReply $reply, User $user) : bool;
}
