<?php

namespace App\Repositories\Interfaces;

use App\User;
use Carbon\Carbon;
use App\Chat;
use App\ChatReply;
use Illuminate\Database\Eloquent\Collection;

interface ChatRepositoryInterface
{
    /**
     * Get all the chats for a specific user.
     *
     * @param User $user
     * @param int $limit
     * @param int $offset
     *
     * @return Collection
     */
    public function getChats(User $user, $limit = 15, $offset = 0) : Collection;

    /**
     * Get a specific chat for a specific user.
     *
     * @param int $id
     * @param User $user
     *
     * @return Chat|null
     */
    public function getChat(int $id, User $user) : ?Chat;

    /**
     * Get all replies in a specific chat (from the given user's point of view).
     *
     * In certain cases, some users delete one or more chat replies. They don't actually
     * delete these replies, instead, their relation to that reply (ConversationReplyUser)
     * gets deleted. In this case, we make sure that we don't fetch these replies.
     *
     * @param Chat $chat
     * @param User $user
     * @param int $limit
     * @param int $offset
     *
     * @return Collection
     */
    public function getReplies(
        Chat $chat,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : Collection;

    /**
     * Get new replies in a specific chat (from the given user's point of view).
     *
     * @param Chat $chat
     * @param User $user
     * @param Carbon $time
     *
     * @return Collection
     */
    public function getNewReplies(
        Chat $chat,
        User $user,
        Carbon $time
    ) : Collection;

    /**
     * Get a specific chat for a specific user along with its replies
     *
     * @param Chat $chat
     * @param User $user
     * @param int $limit
     * @param int $offset
     *
     * @return Chat|null
     */
    public function getChatWithReplies(
        Chat $chat,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : ?Chat;

    /**
     * Get a collection of participants in a specific chat.
     *
     * @param Chat $chat
     *
     * @return \Illuminate\Support\Collection
     */
    public function getChatUsers(Chat $chat) : \Illuminate\Support\Collection;

    /**
     * Count the number of participants in a specific chat.
     *
     * @param Chat $chat
     *
     * @return int
     */
    public function countChatUsers(Chat $chat) : int;

    /**
     * Count the number of users who are related to the given reply.
     *
     * @param ChatReply $reply
     *
     * @return int
     */
    public function countReplyUsers(ChatReply $reply) : int;
}