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

            $chat = new Chat;
            $chat->setCreator($creator);
            $chat->save();

            collect($recipients)->map(function($recipient) use ($chat) {
                $this->addUserToChat($recipient, $chat);
            });

            DB::commit();

            return $chat;

        } catch (\Exception $exception) {
            DB::rollback();
            exit($exception->getMessage());
        }
    }

    public function createReply(
        Chat $chat,
        User $sender,
        string $text,
        Carbon $createdAt = null
    ) : ?ChatReply
    {
        DB::beginTransaction();

        try {

            $reply = new ChatReply();
            $reply->setChat($chat);
            $reply->setSender($sender);
            $reply->setText($text);
            $createdAt !== null ? $reply->setCreatedAt($createdAt) : false;
            $reply->save();

            $users = $this->repository->getChatUsers($chat);

            collect ($users)->map(function ($user) use ($reply) {
                (new ChatReplyUser)
                    ->setUser($user)
                    ->setChatReply($reply)
                    ->save();
            });

            DB::commit();

            return $reply;

        } catch (\Exception $exception) {
            DB::rollback();
            exit($exception->getMessage());
        }
    }

    public function addUserToChat(User $user, Chat $chat) : bool
    {
        return (new ChatUser())
            ->setChat($chat)
            ->setUser($user)
            ->save();
    }

    /**
     * @param Chat $chat
     * @param User $user
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteChat(Chat $chat, User $user) : bool
    {
        if (! $chat || ! $chat instanceof Chat) {
            throw new \Exception('The given chat model does not seem to be a real one!');
        }

        $chatUsersCount = $this->repository->countChatUsers($chat);

        $chatDeleted = ChatUser::where([
            ChatUser::FIELD_CHAT_ID => $chat->getId(),
            ChatUser::FIELD_USER_ID => $user->getId()
        ])->delete();

        // If the last user in a chat deletes his/her own relationship to this chat,
        // we need to delete the chat since it's no longer needed.
        if ($chatUsersCount <= 1 && $chatDeleted) {
            Chat::where(Chat::FIELD_PK, $chat->getId())->delete();
        }

        return $chatDeleted;
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