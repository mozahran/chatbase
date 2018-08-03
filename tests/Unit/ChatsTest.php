<?php

namespace Tests\Unit;

use App\ConversationReply;
use App\Repositories\ChatRepository;
use App\Repositories\Interfaces\ChatRepositoryInterface;
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
        $created = $this->repository->createConversation(1, [1, 2]);

        $this->assertNotNull($created);
    }

    public function testAddUserToConversation()
    {
        $added = $this->repository->addUserToConversation(3, 1);

        $this->assertNotNull($added);
    }

    public function testGetConversations()
    {
        $this->repository->createConversation(1, [1, 2]);
        $this->repository->createConversation(2, [2, 3]);

        $results = $this->repository->getConversations(1);

        $this->assertCount(1, $results);
    }

    public function testGetConversation()
    {
        $c = $this->repository->createConversation(1, [1, 2]);

        $found = $this->repository->getConversation($c->getId(), 1);

        $this->assertNotNull($found);
    }

    public function testDeleteConversation()
    {
        $c = $this->repository->createConversation(1, [1, 2]);

        $deleted = $this->repository->deleteConversation($c->getId(), 1);

        $this->assertTrue($deleted);
    }

    public function testCreateReply()
    {
        $created = $this->repository->createReply(1, 1, 'foo');

        $this->assertNotNull($created);
    }

    public function testGetReplies()
    {
        $conversation = $this->repository->createConversation(1, [1, 2]);
        $this->repository->createReply($conversation->getId(), 1, 'foo');
        $this->repository->createReply($conversation->getId(), 1, 'bar');

        $results = $this->repository->getReplies($conversation->getId(), 1);

        $this->assertCount(2, $results);
    }

    public function testGetNewReplies()
    {
        $conversation = $this->repository->createConversation(1, [1, 2]);

        $yesterday = Carbon::now()->subDay();
        $sinceAnHour = Carbon::now()->subHour();

        $this->repository->createReply($conversation->getId(), 2, 'foo', $yesterday);
        $this->repository->createReply($conversation->getId(), 2, 'bar', $sinceAnHour);

        $results = $this->repository->getNewReplies($conversation->getId(), 1, $yesterday);

        $this->assertCount(1, $results);
    }

    public function testGetConversationWithReplies()
    {
        $conversation = $this->repository->createConversation(1, [1, 2]);
        $this->repository->createReply($conversation->getId(), 1, 'foo');
        $this->repository->createReply($conversation->getId(), 2, 'bar');

        $results = $this->repository->getConversationWithReplies($conversation->getId(), 1);

        $this->assertNotNull($results);
        $this->assertNotNull($results->replies);
    }

    public function testDeleteReply()
    {
        $conversation = $this->repository->createConversation(1, [1,2]);
        $reply = $this->repository->createReply($conversation->getId(), 1, 'foo');

        $deleted = $this->repository->deleteReply($reply->getId(), 1);

        $this->assertTrue($deleted);
    }
}
