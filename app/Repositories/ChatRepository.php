<?php

namespace App\Repositories;

use App\User;
use DB;
use Carbon\Carbon;
use App\Conversation;
use App\ConversationReply;
use App\ConversationReplyUser;
use App\ConversationUser;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository implements ChatRepositoryInterface
{
    public function getConversations(User $user, $limit = 15, $offset = 0) : Collection
    {
        return Conversation::whereHas('users', function ($query) use ($user) {
            return $query->where(ConversationUser::FIELD_USER_ID, $user->getId());
        })->with(['lastReply', 'users' => function($query) use ($user) {
            return $query->where(ConversationUser::FIELD_USER_ID, '!=', $user->getId());
        }])->limit($limit)->offset($offset)->get();
    }

    public function getConversation(int $id, User $user) : ?Conversation
    {
        return Conversation::whereHas('users', function ($query) use ($user) {
            return $query->where(ConversationUser::FIELD_USER_ID, $user->getId());
        })->find($id);
    }

    public function getReplies(
        Conversation $conversation,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : Collection
    {
        return ConversationReply::with('sender')
            ->ofConversation($conversation)
            ->whereHas('recipients', function ($query) use ($user) {
                return $query->where(ConversationReplyUser::FIELD_USER_ID, $user->getId());
            })->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function getNewReplies(
        Conversation $conversation,
        User $user,
        Carbon $time
    ) : Collection
    {
        return ConversationReply::with('sender')
            ->ofConversation($conversation)
            ->whereHas('recipients', function ($query) use ($user) {
                return $query->where(ConversationReplyUser::FIELD_USER_ID, $user->getId());
            })->where('created_at', '>', $time)->get();
    }

    public function getConversationWithReplies(
        Conversation $conversation,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : ?Conversation
    {
        return Conversation::whereHas('users', function ($query) use ($user, $limit, $offset) {
            return $query->where(ConversationUser::FIELD_USER_ID, $user->getId());
        })->with(['replies' => function ($query) use ($user, $limit, $offset) {
            return $query->whereHas('recipients', function ($query) use ($user) {
                return $query->where(ConversationReplyUser::FIELD_USER_ID, $user->getId());
            })->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();
        }])->find($conversation->getId());
    }

    public function getConversationUsers(Conversation $conversation) : \Illuminate\Support\Collection
    {
        $relations = ConversationUser::where(ConversationUser::FIELD_CONVERSATION_ID, $conversation->getId())
            ->with('user')
            ->get();

        return collect($relations)->map(function ($relation) {
            return $relation->user;
        });
    }

    public function countConversationUsers(Conversation $conversation) : int
    {
        return ConversationUser::where([
            ConversationUser::FIELD_CONVERSATION_ID => $conversation->getId(),
        ])->count();
    }
}