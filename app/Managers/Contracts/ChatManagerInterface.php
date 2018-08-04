<?php

namespace App\Managers\Contracts;

use App\User;
use Carbon\Carbon;
use App\Conversation;
use App\ConversationReply;

interface ChatManagerInterface
{
    /**
     * Create a new conversation.
     *
     * @param User $user
     * @param array $users
     *
     * @return Conversation|null
     */
    public function createConversation(User $user, array $users) : ?Conversation;

    /**
     * Create a new reply.
     *
     * @param Conversation $conversation
     * @param User $sender
     * @param string $text
     * @param Carbon|null $createdAt
     *
     * @return ConversationReply|null
     */
    public function createReply(
        Conversation $conversation,
        User $sender,
        string $text,
        Carbon $createdAt = null
    ) : ?ConversationReply;

    /**
     * Add user to an existing conversation.
     *
     * @param User $user
     * @param Conversation $conversation
     *
     * @return bool
     */
    public function addUserToConversation(User $user, Conversation $conversation) : bool;

    /**
     * Delete an existing conversation.
     *
     * @param Conversation $conversation
     * @param User $user
     *
     * @return bool
     */
    public function deleteConversation(Conversation $conversation, User $user) : bool;

    /**
     * Smart delete a reply.
     *
     * This method deletes the relation of the user (ConversationReplyUser)
     * to the actual reply (ConversationReply). If other users are no longer involved in
     * this reply (have no ConversationRelyUser relations), the actual reply gets deleted as well.
     *
     * @param ConversationReply $reply
     * @param User $user
     *
     * @return bool
     */
    public function deleteReply(ConversationReply $reply, User $user) : bool;
}