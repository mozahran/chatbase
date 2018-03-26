<?php

namespace App\Repositories;

use App\User;
use Carbon\Carbon;
use DB;
use App\Conversation;
use App\ConversationReply;
use App\ConversationReplyUser;
use App\ConversationUser;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ChatRepository implements ChatRepositoryInterface
{
    /**
     * Create a new conversation.
     *
     * @param int $creatorId
     * @param $users
     * @return Model|null
     */
    public function createConversation(int $creatorId, array $users)
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

    /**
     * Add a specific user to a conversation.
     *
     * @param int $userId
     * @param int $conversationId
     * @return bool
     */
    public function addUserToConversation(int $userId, int $conversationId) : bool
    {
        return (bool) (new ConversationUser())
            ->setConversationId($conversationId)
            ->setUserId($userId)
            ->save();
    }

    /**
     * Get the conversations that the user either created or is involved in.
     *
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
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

    /**
     * Get a specific conversation from the point of view of a specific user.
     *
     * @param int $id
     * @param int $userId
     * @return Model|null
     */
    public function getConversation(int $id, int $userId)
    {
        return Conversation::whereHas('users', function ($query) use ($userId) {
            return $query->where(ConversationUser::FIELD_USER_ID, $userId);
        })->find($id);
    }

    /**
     * Delete the relationship between a user and a conversation.
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
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

    /**
     *
     * @param int $conversationId
     * @param int $senderId
     * @param string $text
     * @param Carbon|null $createdAt
     * @return Model|null
     */
    public function createReply(int $conversationId, int $senderId, string $text, Carbon $createdAt = null)
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

    /**
     * Get the replies in a specific conversation.
     *
     * @param int $conversationId
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function getReplies(int $conversationId, int $userId, int $limit = 15, int $offset = 0) : Collection
    {
        return ConversationReply::with('sender')
            ->ofConversation($conversationId)
            ->whereHas('recipients', function ($query) use ($userId) {
                return $query->where(ConversationReplyUser::FIELD_USER_ID, $userId);
            })->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Get new replies of a conversation using a time marker.
     *
     * @param int $conversationId
     * @param int $userId
     * @param Carbon $time
     * @return Collection
     */
    public function getNewReplies(int $conversationId, int $userId, Carbon $time) : Collection
    {
        return ConversationReply::with('sender')
            ->ofConversation($conversationId)
            ->whereHas('recipients', function ($query) use ($userId) {
                return $query->where(ConversationReplyUser::FIELD_USER_ID, $userId);
            })->where('created_at', '>', $time)->get();
    }

    /**
     * Get a conversation with its replies for a specific user.
     *
     * @param int $conversationId
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return Model|null
     */
    public function getConversationWithReplies(int $conversationId, int $userId, int $limit = 15, int $offset = 0)
    {
        return Conversation::whereHas('users', function ($query) use ($userId, $limit, $offset) {

            return $query->where(ConversationUser::FIELD_USER_ID, $userId);

        })->with(['replies' => function ($query) use ($userId, $limit, $offset) {

            return $query->whereHas('recipients', function ($query) use ($userId) {
                return $query->where(ConversationReplyUser::FIELD_USER_ID, $userId);
            })->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();

        }])->find($conversationId);
    }

    /**
     * Delete a specific reply for user.
     *
     * @param int $replyId
     * @param int $userId
     * @return bool
     */
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

    /**
     * Get an array of the users involved in a specific conversation.
     *
     * @param int $conversationId
     * @return Collection
     */
    private function getConversationUsers(int $conversationId) : Collection
    {
        return ConversationUser::where(ConversationUser::FIELD_CONVERSATION_ID, $conversationId)->get();
    }
}