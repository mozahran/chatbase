<?php

namespace App\Repositories;

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
    public function getConversations(int $userId, $limit = 15, $offset = 0) : Collection
    {
        return Conversation::whereHas('users', function ($query) use ($userId) {
            return $query->where(ConversationUser::FIELD_USER_ID, $userId);
        })
            ->with(['lastReply', 'users' => function($query) use ($userId) {
                return $query->where(ConversationUser::FIELD_USER_ID, '!=', $userId);
            }])
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function getConversation(int $id, int $userId) : ?Conversation
    {
        return Conversation::whereHas('users', function ($query) use ($userId) {
            return $query->where(ConversationUser::FIELD_USER_ID, $userId);
        })->find($id);
    }

    public function createConversation(int $creatorId, array $users) : ?Conversation
    {
        DB::beginTransaction();

        try {

            $conversation = new Conversation;
            $conversation->setCreatorId($creatorId);
            $conversation->save();

            foreach ($users as $userId) {
                $this->addUserToConversation($userId, $conversation->getId());
            }

            DB::commit();

            return $conversation;

        } catch (\Exception $exception) {
            DB::rollback();
            exit($exception->getMessage());
        }
    }

    public function createReply(
        int $conversationId,
        int $senderId,
        string $text,
        Carbon $createdAt = null
    ) : ?ConversationReply
    {
        DB::beginTransaction();

        try {

            $reply = new ConversationReply();
            $reply->setConversationId($conversationId);
            $reply->setSenderId($senderId);
            $reply->setText($text);
            $createdAt !== null ? $reply->setCreatedAt($createdAt) : false;
            $reply->save();

            $users = $this->getConversationUsers($conversationId);

            foreach ($users as $user) {
                $replyUser = new ConversationReplyUser;
                $replyUser->setUserId($user->getUserId());
                $replyUser->setConversationReplyId($reply->getId());
                $replyUser->save();
            }

            DB::commit();

            return $reply;

        } catch (\Exception $exception) {
            DB::rollback();
            exit($exception->getMessage());
        }
    }

    public function addUserToConversation(int $userId, int $conversationId) : bool
    {
        return (bool) (new ConversationUser())
            ->setConversationId($conversationId)
            ->setUserId($userId)
            ->save();
    }

    public function getReplies(
        int $conversationId,
        int $userId,
        int $limit = 15,
        int $offset = 0
    ) : Collection
    {
        return ConversationReply::with('sender')
            ->ofConversation($conversationId)
            ->whereHas('recipients', function ($query) use ($userId) {
                return $query->where(ConversationReplyUser::FIELD_USER_ID, $userId);
            })->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function getNewReplies(
        int $conversationId,
        int $userId,
        Carbon $time
    ) : Collection
    {
        return ConversationReply::with('sender')
            ->ofConversation($conversationId)
            ->whereHas('recipients', function ($query) use ($userId) {
                return $query->where(ConversationReplyUser::FIELD_USER_ID, $userId);
            })->where('created_at', '>', $time)->get();
    }

    public function getConversationWithReplies(
        int $conversationId,
        int $userId,
        int $limit = 15,
        int $offset = 0
    ) : ?Conversation
    {
        return Conversation::whereHas('users', function ($query) use ($userId, $limit, $offset) {

            return $query->where(ConversationUser::FIELD_USER_ID, $userId);

        })->with(['replies' => function ($query) use ($userId, $limit, $offset) {

            return $query->whereHas('recipients', function ($query) use ($userId) {
                return $query->where(ConversationReplyUser::FIELD_USER_ID, $userId);
            })->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();

        }])->find($conversationId);
    }

    public function deleteConversation(int $id, int $userId) : bool
    {
        $conversationUsersCount = ConversationUser::where([
            ConversationUser::FIELD_CONVERSATION_ID => $id,
            ConversationUser::FIELD_USER_ID => $userId
        ])->count();

        $conversationDeleted = ConversationUser::where([
            ConversationUser::FIELD_CONVERSATION_ID => $id,
            ConversationUser::FIELD_USER_ID => $userId
        ])->delete();

        // If the last user in a conversation deletes his/her own relationship to this conversation,
        // we need to delete the conversation since it's no longer needed.
        if ($conversationUsersCount <= 1 && $conversationDeleted) {
            Conversation::where(Conversation::FIELD_PK, $id)->delete();
        }

        return $conversationDeleted;
    }

    public function deleteReply(int $replyId, int $userId) : bool
    {
        $replyUsersCount = ConversationReplyUser::where([
            ConversationReplyUser::FIELD_CONVERSATION_REPLY_ID => $replyId,
            ConversationReplyUser::FIELD_USER_ID => $userId
        ])->count();

        $replyDeleted = ConversationReplyUser::where(ConversationReplyUser::FIELD_CONVERSATION_REPLY_ID, $replyId)
            ->where(ConversationReplyUser::FIELD_USER_ID, $userId)
            ->delete();

        // Delete the reply if there are no more users involved.
        if ($replyUsersCount <= 1 && $replyDeleted) {
            ConversationReply::where(ConversationReply::FIELD_PK, $replyId)->delete();
        }

        return $replyDeleted;
    }

    private function getConversationUsers(int $conversationId) : Collection
    {
        return ConversationUser::where(ConversationUser::FIELD_CONVERSATION_ID, $conversationId)->get();
    }
}