<?php

namespace App\Repositories\Interfaces;

use App\User;
use Carbon\Carbon;
use App\Conversation;
use App\ConversationReply;
use Illuminate\Database\Eloquent\Collection;

interface ChatRepositoryInterface
{
    public function getConversations(User $user, $limit = 15, $offset = 0) : Collection;

    public function getConversation(int $id, User $user) : ?Conversation;

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

    public function getConversationUsers(Conversation $conversation) : \Illuminate\Support\Collection;

    public function countConversationUsers(Conversation $conversation) : int;

    public function countReplyUsers(ConversationReply $reply) : int;
}