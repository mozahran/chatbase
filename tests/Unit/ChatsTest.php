<?php

namespace Tests\Unit;

use App\Chat;
use App\ChatReply;
use App\ChatReplyUser;
use App\ChatUser;
use App\Managers\ChatManager;
use App\Managers\Contracts\ChatManagerInterface;
use App\Repositories\ChatRepository;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

    public function testCreateChat()
    {
        $creator = $this->createUser();
        $recipient = $this->createUser();

        $created = $this->manager->createChat($creator, [$creator, $recipient]);

        $this->assertNotNull($created);
    }

    public function testAddUserToChat()
    {
        $user = $this->createUser();
        $chat = $this->createChat($user);

        $added = $this->manager->addUserToChat($user, $chat);

        $this->assertNotNull($added);
    }

    public function testGetChats()
    {
        $user = $this->createUser();
        $chat = $this->createChat($user);

        $results = $this->repository->getChats($user);

        $this->assertCount(1, $results);
    }

    public function testGetChat()
    {
        $user = $this->createUser();
        $chat = $this->createChat($user);

        $found = $this->repository->getChat($chat->getId(), $user);

        $this->assertNotNull($found);
    }

    public function testDeleteChat()
    {
        $user = $this->createUser();
        $chat = $this->createChat($user);

        $deleted = $this->manager->deleteChat($chat, $user);

        $this->assertTrue($deleted);
    }

    public function testCountChatUsers()
    {
        $user = $this->createUser();
        $chat = $this->createChat($user);

        $count = $this->repository->countChatUsers($chat);

        $this->assertEquals(1, $count);
    }

    public function testCountReplyUsers()
    {
        $user = $this->createUser();
        $recipients = [$this->createUser(), $this->createUser()];
        $chat = $this->createChat($user);
        $reply = $this->createReply($chat, $user, $recipients, 'foo');

        $count = $this->repository->countReplyUsers($reply);

        $this->assertEquals(3, $count);
    }

    public function testCreateReply()
    {
        $user = $this->createUser();
        $recipients = [$this->createUser(), $this->createUser()];
        $chat = $this->createChat($user, $recipients);

        $created = $this->manager->createReply($chat, $user, 'foo');

        $this->assertNotNull($created);
    }

    public function testGetReplies()
    {
        $user = $this->createUser();
        $recipients = [$this->createUser(), $this->createUser()];
        $chat = $this->createChat($user);

        $this->createReply($chat, $user, $recipients, 'foo');
        $this->createReply($chat, $user, $recipients, 'bar');

        $results = $this->repository->getReplies($chat, $user);

        $this->assertCount(2, $results);
    }

    public function testGetNewReplies()
    {
        $user = $this->createUser();
        $recipients = [$this->createUser(), $this->createUser()];
        $chat = $this->createChat($user);

        $yesterday = Carbon::now()->subDay();
        $sinceAnHour = Carbon::now()->subHour();

        $this->createReply($chat, $user, $recipients, 'foo', $yesterday);
        $this->createReply($chat, $user, $recipients, 'bar', $sinceAnHour);

        $results = $this->repository->getNewReplies($chat, $user, $yesterday);

        $this->assertCount(1, $results);
    }

    public function testGetChatWithReplies()
    {
        $user = $this->createUser();
        $recipients = [$this->createUser(), $this->createUser()];
        $chat = $this->createChat($user);

        $this->createReply($chat, $user, $recipients, 'foo');
        $this->createReply($chat, $user, $recipients, 'bar');

        $results = $this->repository->getChatWithReplies($chat, $user);

        $this->assertNotNull($results);
        $this->assertNotNull($results->replies);
    }

    public function testDeleteReply()
    {
        $user = $this->createUser();
        $recipients = [$this->createUser(), $this->createUser()];
        $chat = $this->createChat($user);
        $reply = $this->createReply($chat, $user, $recipients, 'foo');

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

    private function createChat(User $creator, array $users = null)
    {
        $chat = factory(Chat::class)->create([
            Chat::FIELD_CREATOR_ID => $creator->getId(),
        ]);

        $users[] = $creator;

        collect($users)->map(function (User $user) use ($chat) {
            factory(ChatUser::class)->create([
                ChatUser::FIELD_CHAT_ID => $chat->getId(),
                ChatUser::FIELD_USER_ID => $user->getId(),
            ]);
        });

        return $chat;
    }

    private function createReply(
        Chat $chat,
        User $user,
        array $recipients,
        string $text,
        Carbon $createdAt = null
    ) : ?ChatReply {
        $reply = factory(ChatReply::class)->create([
            ChatReply::FIELD_CHAT_ID   => $chat->getId(),
            ChatReply::FIELD_SENDER_ID => $user->getId(),
            ChatReply::FIELD_TEXT      => $text,
            ChatReply::CREATED_AT      => $createdAt,
        ]);

        $recipients[] = $user;

        collect($recipients)->map(function (User $user) use ($reply) {
            factory(ChatReplyUser::class)->create([
                ChatReplyUser::FIELD_USER_ID       => $user->getId(),
                ChatReplyUser::FIELD_CHAT_REPLY_ID => $reply->getId(),
            ]);
        });

        return $reply;
    }
}
