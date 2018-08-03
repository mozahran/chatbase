<?php

namespace App\Repositories\Interfaces;

use App\Conversation;
use App\ConversationReply;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface ChatRepositoryInterface
{
    public function getConversations(int $userId, $limit = 15, $offset = 0) : Collection;
    public function getConversation(int $id, int $userId) : ?Conversation;
    public function createConversation(int $creatorId, array $users) : ?Conversation;
    public function createReply(int $conversationId, int $senderId, string $text) : ?ConversationReply;
    public function addUserToConversation(int $userId, int $conversationId) : bool;
    public function getReplies(int $conversationId, int $userId, int $limit = 15, int $offset = 0) : Collection;
    public function getNewReplies(int $conversationId, int $userId, Carbon $time) : Collection;
    public function getConversationWithReplies(int $conversationId, int $userId, int $limit = 15, int $offset = 0) : ?Conversation;
    public function deleteConversation(int $id, int $userId) : bool;
    public function deleteReply(int $replyId, int $userId) : bool;
}