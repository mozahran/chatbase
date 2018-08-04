<?php

namespace App\Repositories\Interfaces;

use App\User;
use Carbon\Carbon;
use App\Conversation;
use App\ConversationReply;
use Illuminate\Database\Eloquent\Collection;

interface ChatRepositoryInterface
{
    /**
     * Get all the conversations for a specific user.
     *
     * @param User $user
     * @param int $limit
     * @param int $offset
     *
     * @return Collection
     */
    public function getConversations(User $user, $limit = 15, $offset = 0) : Collection;

    /**
     * Get a specific conversation for a specific user.
     *
     * @param int $id
     * @param User $user
     *
     * @return Conversation|null
     */
    public function getConversation(int $id, User $user) : ?Conversation;

    /**
     * Get all replies in a specific conversation (from the given user's point of view).
     *
     * In certain cases, some users delete one or more chat replies. They don't actually
     * delete these replies, instead, their relation to that reply (ConversationReplyUser)
     * gets deleted. In this case, we make sure that we don't fetch these replies.
     *
     * @param Conversation $conversation
     * @param User $user
     * @param int $limit
     * @param int $offset
     *
     * @return Collection
     */
    public function getReplies(
        Conversation $conversation,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : Collection;

    /**
     * Get new replies in a specific conversation (from the given user's point of view).
     *
     * @param Conversation $conversation
     * @param User $user
     * @param Carbon $time
     *
     * @return Collection
     */
    public function getNewReplies(
        Conversation $conversation,
        User $user,
        Carbon $time
    ) : Collection;

    /**
     * Get a specific conversation for a specific user along with its replies
     *
     * @param Conversation $conversation
     * @param User $user
     * @param int $limit
     * @param int $offset
     *
     * @return Conversation|null
     */
    public function getConversationWithReplies(
        Conversation $conversation,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : ?Conversation;

    /**
     * Get a collection of participants in a specific conversation.
     *
     * @param Conversation $conversation
     *
     * @return \Illuminate\Support\Collection
     */
    public function getConversationUsers(Conversation $conversation) : \Illuminate\Support\Collection;

    /**
     * Count the number of participants in a specific conversation.
     *
     * @param Conversation $conversation
     *
     * @return int
     */
    public function countConversationUsers(Conversation $conversation) : int;

    /**
     * Count the number of users who are related to the given reply.
     *
     * @param ConversationReply $reply
     *
     * @return int
     */
    public function countReplyUsers(ConversationReply $reply) : int;
}