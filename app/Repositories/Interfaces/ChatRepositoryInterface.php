<?php

namespace App\Repositories\Interfaces;

use App\Conversation;
use App\ConversationReply;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface ChatRepositoryInterface
{
    public function getConversations(User $user, $limit = 15, $offset = 0) : Collection;

    public function getConversation(int $id, User $user) : ?Conversation;

    public function createConversation(User $user, array $users) : ?Conversation;

    public function createReply(
        Conversation $conversation,
        User $sender,
        string $text,
        Carbon $createdAt = null
    ) : ?ConversationReply;

    public function addUserToConversation(User $user, Conversation $conversation) : bool;

    public function getReplies(
        Conversation $conversation,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : Collection;

    public function getNewReplies(
        Conversation $conversation,
        User $user,
        Carbon $time
    ) : Collection;

    public function getConversationWithReplies(
        Conversation $conversation,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : ?Conversation;

    public function deleteConversation(int $id, User $user) : bool;

    public function deleteReply(ConversationReply $reply, User $user) : bool;
}