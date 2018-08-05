<?php

namespace App\Managers;

use DB;
use App\User;
use Carbon\Carbon;
use App\Chat;
use App\ChatUser;
use App\ChatReply;
use App\ChatReplyUser;
use App\Repositories\ChatRepository;
use App\Managers\Contracts\ChatManagerInterface;
use App\Repositories\Interfaces\ChatRepositoryInterface;

class ChatManager implements ChatManagerInterface
{
    /**
     * @var ChatRepositoryInterface
     */
    private $repository;

    public function __construct()
    {
        $this->repository = app(ChatRepository::class);
    }

    /**
     * @inheritdoc
     */
    public function createChat(User $creator, array $recipients) : ?Chat
    {
        DB::beginTransaction();

        try {

            $conversation = new Chat;
            $conversation->setCreator($creator);
            $conversation->save();

            collect($recipients)->map(function($recipient) use ($conversation) {
                $this->addUserToChat($recipient, $conversation);
            });

            DB::commit();

            return $conversation;

        } catch (\Exception $exception) {
            DB::rollback();
            exit($exception->getMessage());
        }
    }

    public function createReply(
        Chat $conversation,
        User $sender,
        string $text,
        Carbon $createdAt = null
    ) : ?ChatReply
    {
        DB::beginTransaction();

        try {

            $reply = new ChatReply();
            $reply->setConversation($conversation);
            $reply->setSender($sender);
            $reply->setText($text);
            $createdAt !== null ? $reply->setCreatedAt($createdAt) : false;
            $reply->save();

            $users = $this->repository->getChatUsers($conversation);

            collect ($users)->map(function ($user) use ($reply) {
                (new ChatReplyUser)
                    ->setUser($user)
                    ->setConversationReply($reply)
                    ->save();
            });

            DB::commit();

            return $reply;

        } catch (\Exception $exception) {
            DB::rollback();
            exit($exception->getMessage());
        }
    }

    public function addUserToChat(User $user, Chat $conversation) : bool
    {
        return (new ChatUser())
            ->setConversation($conversation)
            ->setUser($user)
            ->save();
    }

    /**
     * @param Chat $conversation
     * @param User $user
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteChat(Chat $conversation, User $user) : bool
    {
        if (! $conversation || ! $conversation instanceof Chat) {
            throw new \Exception('The given conversation model does not seem to be a real one!');
        }

        $conversationUsersCount = $this->repository->countChatUsers($conversation);

        $conversationDeleted = ChatUser::where([
            ChatUser::FIELD_CHAT_ID => $conversation->getId(),
            ChatUser::FIELD_USER_ID => $user->getId()
        ])->delete();

        // If the last user in a conversation deletes his/her own relationship to this conversation,
        // we need to delete the conversation since it's no longer needed.
        if ($conversationUsersCount <= 1 && $conversationDeleted) {
            Chat::where(Chat::FIELD_PK, $conversation->getId())->delete();
        }

        return $conversationDeleted;
    }

    public function deleteReply(ChatReply $reply, User $user) : bool
    {
        $replyUsersCount = $this->repository->countReplyUsers($reply);

        $replyDeleted = ChatReplyUser::where(ChatReplyUser::FIELD_CHAT_REPLY_ID, $reply->getId())
            ->where(ChatReplyUser::FIELD_USER_ID, $user->getId())
            ->delete();

        // Delete the reply if there are no more users involved.
        if ($replyUsersCount <= 1 && $replyDeleted) {
            ChatReply::where(ChatReply::FIELD_PK, $reply->getId())->delete();
        }

        return $replyDeleted;
    }
}