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

    public function createConversation(User $creator, array $users) : ?Conversation
    {
        DB::beginTransaction();

        try {

            $conversation = new Conversation;
            $conversation->setCreator($creator);
            $conversation->save();

            foreach ($users as $user) {
                $this->addUserToConversation($user, $conversation);
            }

            DB::commit();

            return $conversation;

        } catch (\Exception $exception) {
            DB::rollback();
            exit($exception->getMessage());
        }
    }

    public function createReply(
        Conversation $conversation,
        User $sender,
        string $text,
        Carbon $createdAt = null
    ) : ?ConversationReply
    {
        DB::beginTransaction();

        try {

            $reply = new ConversationReply();
            $reply->setConversation($conversation);
            $reply->setSender($sender);
            $reply->setText($text);
            $createdAt !== null ? $reply->setCreatedAt($createdAt) : false;
            $reply->save();

            $users = $this->getConversationUsers($conversation);

            foreach ($users as $user) {
                $replyUser = new ConversationReplyUser;
                $replyUser->setUser($user);
                $replyUser->setConversationReply($reply);
                $replyUser->save();
            }

            DB::commit();

            return $reply;

        } catch (\Exception $exception) {
            DB::rollback();
            exit($exception->getMessage());
        }
    }

    public function addUserToConversation(User $user, Conversation $conversation) : bool
    {
        return (new ConversationUser())
            ->setConversation($conversation)
            ->setUser($user)
            ->save();
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

    public function deleteConversation(int $id, User $user) : bool
    {
        $conversationUsersCount = ConversationUser::where([
            ConversationUser::FIELD_CONVERSATION_ID => $id,
            ConversationUser::FIELD_USER_ID => $user->getId()
        ])->count();

        $conversationDeleted = ConversationUser::where([
            ConversationUser::FIELD_CONVERSATION_ID => $id,
            ConversationUser::FIELD_USER_ID => $user->getId()
        ])->delete();

        // If the last user in a conversation deletes his/her own relationship to this conversation,
        // we need to delete the conversation since it's no longer needed.
        if ($conversationUsersCount <= 1 && $conversationDeleted) {
            Conversation::where(Conversation::FIELD_PK, $id)->delete();
        }

        return $conversationDeleted;
    }

    public function deleteReply(ConversationReply $reply, User $user) : bool
    {
        $replyUsersCount = ConversationReplyUser::where([
            ConversationReplyUser::FIELD_CONVERSATION_REPLY_ID => $reply->getId(),
            ConversationReplyUser::FIELD_USER_ID => $user->getId()
        ])->count();

        $replyDeleted = ConversationReplyUser::where(ConversationReplyUser::FIELD_CONVERSATION_REPLY_ID, $reply->getId())
            ->where(ConversationReplyUser::FIELD_USER_ID, $user->getId())
            ->delete();

        // Delete the reply if there are no more users involved.
        if ($replyUsersCount <= 1 && $replyDeleted) {
            ConversationReply::where(ConversationReply::FIELD_PK, $reply->getId())->delete();
        }

        return $replyDeleted;
    }

    private function getConversationUsers(Conversation $conversation) : Collection
    {
        $users = new Collection();

        $relations = ConversationUser::where(ConversationUser::FIELD_CONVERSATION_ID, $conversation->getId())
            ->with('user')
            ->get();

        foreach ($relations as $relation) {
            $users->add($relation->user);
        }

        return $users;
    }
}