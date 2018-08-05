<?php

namespace App\Repositories;

use DB;
use App\User;
use Carbon\Carbon;
use App\Chat;
use App\ChatUser;
use App\ChatReply;
use App\ChatReplyUser;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\ChatRepositoryInterface;

class ChatRepository implements ChatRepositoryInterface
{
    public function getChats(User $user, $limit = 15, $offset = 0) : Collection
    {
        return Chat::whereHas('users', function ($query) use ($user) {
            return $query->where(ChatUser::FIELD_USER_ID, $user->getId());
        })->with(['lastReply', 'users' => function($query) use ($user) {
            return $query->where(ChatUser::FIELD_USER_ID, '!=', $user->getId());
        }])->limit($limit)->offset($offset)->get();
    }

    public function getChat(int $id, User $user) : ?Chat
    {
        return Chat::whereHas('users', function ($query) use ($user) {
            return $query->where(ChatUser::FIELD_USER_ID, $user->getId());
        })->find($id);
    }

    public function getReplies(
        Chat $conversation,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : Collection
    {
        return ChatReply::with('sender')
            ->ofConversation($conversation)
            ->whereHas('recipients', function ($query) use ($user) {
                return $query->where(ChatReplyUser::FIELD_USER_ID, $user->getId());
            })->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function getNewReplies(
        Chat $conversation,
        User $user,
        Carbon $time
    ) : Collection
    {
        return ChatReply::with('sender')
            ->ofConversation($conversation)
            ->whereHas('recipients', function ($query) use ($user) {
                return $query->where(ChatReplyUser::FIELD_USER_ID, $user->getId());
            })->where('created_at', '>', $time)->get();
    }

    public function getChatWithReplies(
        Chat $conversation,
        User $user,
        int $limit = 15,
        int $offset = 0
    ) : ?Chat
    {
        return Chat::whereHas('users', function ($query) use ($user, $limit, $offset) {
            return $query->where(ChatUser::FIELD_USER_ID, $user->getId());
        })->with(['replies' => function ($query) use ($user, $limit, $offset) {
            return $query->whereHas('recipients', function ($query) use ($user) {
                return $query->where(ChatReplyUser::FIELD_USER_ID, $user->getId());
            })->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();
        }])->find($conversation->getId());
    }

    public function getChatUsers(Chat $conversation) : \Illuminate\Support\Collection
    {
        $relations = ChatUser::where(ChatUser::FIELD_CHAT_ID, $conversation->getId())
            ->with('user')
            ->get();

        return collect($relations)->map(function ($relation) {
            return $relation->user;
        });
    }

    public function countChatUsers(Chat $conversation) : int
    {
        return ChatUser::where([
            ChatUser::FIELD_CHAT_ID => $conversation->getId(),
        ])->count();
    }

    public function countReplyUsers(ChatReply $reply) : int
    {
        return $reply->recipients()->count();
    }
}