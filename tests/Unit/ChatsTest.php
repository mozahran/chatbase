<?php

namespace Tests\Unit;

use App\Conversation;
use App\ConversationReply;
use App\ConversationReplyUser;
use App\ConversationUser;
use App\Managers\ChatManager;
use App\Managers\Contracts\ChatManagerInterface;
use App\Repositories\ChatRepository;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var ChatRepositoryInterface
     */
    private $repository;

    /**
     * @var ChatManagerInterface
     */
    private $manager;

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->repository = app(ChatRepository::class);
        $this->manager = app(ChatManager::class);
    }

    public function testCreateConversation()
    {
        $creator = $this->createUser();
        $recipient = $this->createUser();

        $created = $this->manager->createConversation($creator, [$creator, $recipient]);

        $this->assertNotNull($created);
    }

    public function testAddUserToConversation()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);

        $added = $this->manager->addUserToConversation($user, $conversation);

        $this->assertNotNull($added);
    }

    public function testGetConversations()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);

        $results = $this->repository->getConversations($user);

        $this->assertCount(1, $results);
    }

    public function testGetConversation()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);

        $found = $this->repository->getConversation($conversation->getId(), $user);

        $this->assertNotNull($found);
    }

    public function testDeleteConversation()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);

        $deleted = $this->manager->deleteConversation($conversation, $user);

        $this->assertTrue($deleted);
    }

    public function testCountConversationUsers()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);

        $count = $this->repository->countConversationUsers($conversation);

        $this->assertEquals(1, $count);
    }

    public function testCountReplyUsers()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);
        $reply = $this->createReply($conversation, $user, 'foo');

        $count = $this->repository->countReplyUsers($reply);

        $this->assertEquals(1, $count);
    }

    public function testCreateReply()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);

        $created = $this->manager->createReply($conversation, $user, 'foo');

        $this->assertNotNull($created);
    }

    public function testGetReplies()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);

        $this->createReply($conversation, $user, 'foo');
        $this->createReply($conversation, $user, 'bar');

        $results = $this->repository->getReplies($conversation, $user);

        $this->assertCount(2, $results);
    }

    public function testGetNewReplies()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);

        $yesterday = Carbon::now()->subDay();
        $sinceAnHour = Carbon::now()->subHour();

        $this->createReply($conversation, $user, 'foo', $yesterday);
        $this->createReply($conversation, $user, 'bar', $sinceAnHour);

        $results = $this->repository->getNewReplies($conversation, $user, $yesterday);

        $this->assertCount(1, $results);
    }

    public function testGetConversationWithReplies()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);

        $this->createReply($conversation, $user, 'foo');
        $this->createReply($conversation, $user, 'bar');

        $results = $this->repository->getConversationWithReplies($conversation, $user);

        $this->assertNotNull($results);
        $this->assertNotNull($results->replies);
    }

    public function testDeleteReply()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation($user);
        $reply = $this->createReply($conversation, $user, 'foo');

        $deleted = $this->manager->deleteReply($reply, $user);

        $this->assertTrue($deleted);
    }

    // ----------------------------------------------------------------------
    // Dummy Factory Methods
    // ----------------------------------------------------------------------

    private function createUser()
    {
        return factory(User::class)->create();
    }

    private function createConversation(User $user)
    {
        $conversation = factory(Conversation::class)->create([
            Conversation::FIELD_CREATOR_ID => $user->getId(),
        ]);

        factory(ConversationUser::class)->create([
            ConversationUser::FIELD_CONVERSATION_ID => $conversation->getId(),
            ConversationUser::FIELD_USER_ID => $user->getId(),
        ]);

        return $conversation;
    }

    private function createReply(Conversation $conversation, User $user, string $text, Carbon $createdAt = null)
    {
        $reply = factory(ConversationReply::class)->create([
            ConversationReply::FIELD_CONVERSATION_ID => $conversation->getId(),
            ConversationReply::FIELD_SENDER_ID => $user->getId(),
            ConversationReply::FIELD_TEXT => $text,
            ConversationReply::CREATED_AT => $createdAt
        ]);

        factory(ConversationReplyUser::class)->create([
            ConversationReplyUser::FIELD_USER_ID => $user->getId(),
            ConversationReplyUser::FIELD_CONVERSATION_REPLY_ID => $reply->getId(),
        ]);

        return $reply;
    }
}
