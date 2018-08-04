<?php

namespace App\Managers;

use DB;
use App\User;
use Carbon\Carbon;
use App\Conversation;
use App\ConversationUser;
use App\ConversationReply;
use App\ConversationReplyUser;
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
    public function createConversation(User $creator, array $recipients) : ?Conversation
    {
        DB::beginTransaction();

        try {

            $conversation = new Conversation;
            $conversation->setCreator($creator);
            $conversation->save();

            collect($recipients)->map(function($recipient) use ($conversation) {
                $this->addUserToConversation($recipient, $conversation);
            });

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

            $users = $this->repository->getConversationUsers($conversation);

            collect ($users)->map(function ($user) use ($reply) {
                (new ConversationReplyUser)
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

    public function addUserToConversation(User $user, Conversation $conversation) : bool
    {
        return (new ConversationUser())
            ->setConversation($conversation)
            ->setUser($user)
            ->save();
    }

    /**
     * @param Conversation $conversation
     * @param User $user
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteConversation(Conversation $conversation, User $user) : bool
    {
        if (! $conversation || ! $conversation instanceof Conversation) {
            throw new \Exception('The given conversation model does not seem to be a real one!');
        }

        $conversationUsersCount = $this->repository->countConversationUsers($conversation);

        $conversationDeleted = ConversationUser::where([
            ConversationUser::FIELD_CONVERSATION_ID => $conversation->getId(),
            ConversationUser::FIELD_USER_ID => $user->getId()
        ])->delete();

        // If the last user in a conversation deletes his/her own relationship to this conversation,
        // we need to delete the conversation since it's no longer needed.
        if ($conversationUsersCount <= 1 && $conversationDeleted) {
            Conversation::where(Conversation::FIELD_PK, $conversation->getId())->delete();
        }

        return $conversationDeleted;
    }

    public function deleteReply(ConversationReply $reply, User $user) : bool
    {
        $replyUsersCount = $this->repository->countReplyUsers($reply);

        $replyDeleted = ConversationReplyUser::where(ConversationReplyUser::FIELD_CONVERSATION_REPLY_ID, $reply->getId())
            ->where(ConversationReplyUser::FIELD_USER_ID, $user->getId())
            ->delete();

        // Delete the reply if there are no more users involved.
        if ($replyUsersCount <= 1 && $replyDeleted) {
            ConversationReply::where(ConversationReply::FIELD_PK, $reply->getId())->delete();
        }

        return $replyDeleted;
    }
}