<?php

namespace Tests\Unit;

use App\Conversation;
use App\ConversationReply;
use App\Repositories\ChatRepository;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatsTest extends TestCase
{
    use RefreshDatabase;

    /** @var ChatRepositoryInterface */
    private $repository;

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->repository = app(ChatRepository::class);
    }

    public function testCreateConversation()
    {
        $creator = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $created = $this->repository->createConversation($creator, [$creator, $recipient]);

        $this->assertNotNull($created);
    }

    public function testAddUserToConversation()
    {
        $user = factory(User::class)->create();

        $conversation = factory(Conversation::class)->create([
            Conversation::FIELD_CREATOR_ID => $user->getId(),
        ]);

        $added = $this->repository->addUserToConversation($user, $conversation);

        $this->assertNotNull($added);
    }

    public function testGetConversations()
    {
        $firstUser = factory(User::class)->create();
        $secondUser = factory(User::class)->create();
        $thirdUser = factory(User::class)->create();

        $this->repository->createConversation($firstUser, [$firstUser, $secondUser]);
        $this->repository->createConversation($secondUser, [$secondUser, $thirdUser]);

        $results = $this->repository->getConversations($firstUser);

        $this->assertCount(1, $results);
    }

    public function testGetConversation()
    {
        $creator = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $c = $this->repository->createConversation($creator, [$creator, $recipient]);

        $found = $this->repository->getConversation($c->getId(), $creator);

        $this->assertNotNull($found);
    }

    public function testDeleteConversation()
    {
        $creator = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $c = $this->repository->createConversation($creator, [$creator, $recipient]);

        $deleted = $this->repository->deleteConversation($c->getId(), $creator);

        $this->assertTrue($deleted);
    }

    public function testCreateReply()
    {
        $sender = factory(User::class)->create();

        $conversation = factory(Conversation::class)->create([
            Conversation::FIELD_CREATOR_ID => $sender->getId(),
        ]);

        $created = $this->repository->createReply($conversation, $sender, 'foo');

        $this->assertNotNull($created);
    }

    public function testGetReplies()
    {
        $creator = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $conversation = $this->repository->createConversation($creator, [$creator, $recipient]);

        $this->repository->createReply($conversation, $creator, 'foo');
        $this->repository->createReply($conversation, $creator, 'bar');

        $results = $this->repository->getReplies($conversation, $creator);

        $this->assertCount(2, $results);
    }

    public function testGetNewReplies()
    {
        $firstUser = factory(User::class)->create();
        $secondUser = factory(User::class)->create();

        $conversation = $this->repository->createConversation($firstUser, [$firstUser, $secondUser]);

        $yesterday = Carbon::now()->subDay();
        $sinceAnHour = Carbon::now()->subHour();

        $this->repository->createReply($conversation, $secondUser, 'foo', $yesterday);
        $this->repository->createReply($conversation, $secondUser, 'bar', $sinceAnHour);

        $results = $this->repository->getNewReplies($conversation, $firstUser, $yesterday);

        $this->assertCount(1, $results);
    }

    public function testGetConversationWithReplies()
    {
        $firstUser = factory(User::class)->create();
        $secondUser = factory(User::class)->create();

        $conversation = $this->repository->createConversation($firstUser, [$firstUser, $secondUser]);

        $this->repository->createReply($conversation, $firstUser, 'foo');
        $this->repository->createReply($conversation, $secondUser, 'bar');

        $results = $this->repository->getConversationWithReplies($conversation, $firstUser);

        $this->assertNotNull($results);
        $this->assertNotNull($results->replies);
    }

    public function testDeleteReply()
    {
        $firstUser = factory(User::class)->create();
        $secondUser = factory(User::class)->create();

        $conversation = $this->repository->createConversation($firstUser, [$firstUser, $secondUser]);

        $reply = $this->repository->createReply($conversation, $firstUser, 'foo');

        $deleted = $this->repository->deleteReply($reply, $firstUser);

        $this->assertTrue($deleted);
    }
}
