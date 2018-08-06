## About Chatbase

Chatbase is a free open source code for integration with your Laravel projects. 
This chat is unit-tested so make sure you run the tests each time you make modifications to the core functions.
You can easily swap the ChatRepositoryInterface with your own interfaces if you like, but MAKE SURE you bind your new interface to the concrete class `ChatRepository` in `AppServiceProvider.php` line `18`.

### Features

- Single chat (one-to-one)
- Group chat (one-to-many)

## How to use

The first thing you need to do is to inject the ChatRepositoryInterface to the function/method you want to use.

```php
Route::get('/test', function (\App\Repositories\Interfaces\ChatRepositoryInterface $chatRepository) {
    //
}
```

Note: You are free to create an alias for Chatbase or a service provider. There are no restrictions at all.


### Create A Chat

The `createChat` method in ChatRepository take two arguments. The first one an integer for the creator ID and the second param is the array of users involved in the chat (the creator ID must be included).
That way you can have a one-to-one chat by passing two user IDs or one-to-many by passing three or more user IDs.

```php
$chat = $chatRepository->createChat(1, [1, 2]);
```

##### Add users to a chat

You can add users to a created chat at any time, just like facebook group chats. This method takes two parameters: the user ID and the chats ID respectively.

```php
$chatRepository->addUserToChat(3, 1)
```

### Get A Chat

The `getChat` method takes the logged in `userId` since each user can delete replies on his part while not affecting other users involved in the chat.

```php
$chatId = 1;
$userId = 1;

$chat = $chatRepository->getChat($chatId, $userId);
```

If you want to get the replies along with the chat call the `getChatWithReplies` method with the same parameters as in the `getChat` method.

### Get Chats

To get the chats of a specific user:


```php
$chats = $chatRepository->getChats(1);
```

You can limit the results by passing `limit` and `offset` params to the method after the `userId`.

### Delete A Chat

When a user deletes a chat, other users involved in the chat are not affected. What really happens is that the relationship created for that user in the `chat_user` table gets deleted.

```php
$chatRepositoy->deleteChat(1);
```

### Create A Reply

In order to create a reply you need to pass the `chatId`, `userId` & the `text` of the reply respectively.

```php
$chatRepository->createReply(1, 2, "Hello World!");
```

### Get Replies

The `userId` here is used to get the replies form the user's point of view (to avoid fetching replies that the user deleted). 

```php
$chatId = 1;
$userId = 1;

$replies = $chatRepository->getReplies($chatId, $userId);
```

You can limit the replies by passing `limit` and `offset` params to the method after the `userId`.

### Get New Replies

You can use this method in a real-time chat to fetch new replies using a time marker.

```php
$chatId = 1;
$userId = 1;
$timeMarker = \Carbon\Carbon::now();

$newReplies = $chatRepository->getNewReplies($chatId, $userId, $timeMarker);
```

### Delete A Reply

Once again, this won't delete the actual reply stored in `chat_replies` table. It just deletes the relationship for the user with this reply. The reply only gets deleted if there are no users interested in (have relationships with) this reply.

```php
$replyId = 1;
$userId = 1;

$chatReply->deleteReply($replyId, $userId);
```
