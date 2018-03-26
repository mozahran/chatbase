<?php

namespace App\Repositories\Interfaces;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ChatRepositoryInterface
{
    /**
     * Create a new conversation.
     *
     * @param int $creatorId
     * @param $users
     * @return Model|null
     */
    public function createConversation(int $creatorId, array $users);

    /**
     * Add a specific user to a conversation.
     *
     * @param int $userId
     * @param int $conversationId
     * @return bool
     */
    public function addUserToConversation(int $userId, int $conversationId) : bool;

    /**
     * Get the conversations that the user either created or is involved in.
     *
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function getConversations(int $userId, $limit = 15, $offset = 0) : Collection;

    /**
     * Get a specific conversation from the point of view of a specific user.
     *
     * @param int $id
     * @param int $userId
     * @return Model|null
     */
    public function getConversation(int $id, int $userId);

    /**
     * Delete a specific conversation that is the user involved in.
     *
     * @param int $id
     * @return bool
     */
    public function deleteConversation(int $id) : bool;

    /**
     *
     * @param int $conversationId
     * @param int $senderId
     * @param string $text
     * @return Model|null
     */
    public function createReply(int $conversationId, int $senderId, string $text);

    /**
     * Get the replies in a specific conversation.
     *
     * @param int $conversationId
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function getReplies(int $conversationId, int $userId, int $limit = 15, int $offset = 0) : Collection;

    /**
     * Get new replies of a conversation using a time marker.
     *
     * @param int $conversationId
     * @param int $userId
     * @param Carbon $time
     * @return Collection
     */
    public function getNewReplies(int $conversationId, int $userId, Carbon $time) : Collection;

    /**
     * Get a conversation with its replies for a specific user.
     *
     * @param int $conversationId
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return Model|null
     */
    public function getConversationWithReplies(int $conversationId, int $userId, int $limit = 15, int $offset = 0);

    /**
     * Delete a specific reply for user.
     *
     * @param int $replyId
     * @param int $userId
     * @return bool
     */
    public function deleteReply(int $replyId, int $userId) : bool;
}