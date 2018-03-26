## About Chatbase

Chatbase is a free open source code for integration with your Laravel projects. 
This chat is unit-tested so make sure you run the tests each time you make modifications to the core functions.
You can easily swap the ChatRepositoryInterface with your own interfaces if you like, but MAKE SURE you bind your new interface to the concrete class `ChatRepository` in `AppServiceProvider.php` line `18`.

### Features

- Single conversation (one-to-one)
- Group conversation (one-to-many)

## How to use

The first thing you need to do is to inject the ChatRepositoryInterface to the function/method you want to use.

```php
Route::get('/test', function (\App\Repositories\Interfaces\ChatRepositoryInterface $chatRepository) {
    //
}
```

Note: You are free to create an alias for Chatbase or a service provider. There are no restrictions at all.


### Create A Conversation

The `createConversation` method in ChatRepository take two arguments. The first one an integer for the creator ID and the second param is the array of users involved in the conversation (the creator ID must be included).
That way you can have a one-to-one chat by passing two user IDs or one-to-many by passing three or more user IDs.

```php
$conversation = $chatRepository->createConversation(1, [1, 2]);
```

##### Add users to a conversation

You can add users to a created conversation at any time, just like facebook group conversations. This method takes two parameters: the user ID and the conversations ID respectively.

```php
$chatRepository->addUserToConversation(3, 1)
```

### Get A Conversation

The `getConversation` method takes the logged in `userId` since each user can delete replies on his part while not affecting other users involved in the conversation.

```php
$conversationId = 1;
$userId = 1;

$conversation = $chatRepository->getConversation($conversationId, $userId);
```

If you want to get the replies along with the conversation call the `getConversationWithReplies` method with the same parameters as in the `getConversation` method.

### Get Conversations

To get the conversations of a specific user:


```php
$conversations = $chatRepository->getConversations(1);
```

You can limit the results by passing `limit` and `offset` params to the method after the `userId`.

### Delete A Conversation

When a user deletes a conversation, other users involved in the conversation are not affected. What really happens is that the relationship created for that user in the `conversation_user` table gets deleted.

```php
$chatRepositoy->deleteConversation(1);
```

### Create A Reply

In order to create a reply you need to pass the `conversationId`, `userId` & the `text` of the reply respectively.

```php
$chatRepository->createReply(1, 2, "Hello World!");
```

### Get Replies

The `userId` here is used to get the replies form the user's point of view (to avoid fetching replies that the user deleted). 

```php
$conversationId = 1;
$userId = 1;

$replies = $chatRepository->getReplies($conversationId, $userId);
```

You can limit the replies by passing `limit` and `offset` params to the method after the `userId`.

### Get New Replies

You can use this method in a real-time conversation to fetch new replies using a time marker.

```php
$conversationId = 1;
$userId = 1;
$timeMarker = \Carbon\Carbon::now();

$newReplies = $chatRepository->getNewReplies($conversationId, $userId, $timeMarker);
```

### Delete A Reply

Once again, this won't delete the actual reply stored in `conversation_replies` table. It just deletes the relationship for the user with this reply. The reply only gets deleted if there are no users interested in (have relationships with) this reply.

```php
$replyId = 1;
$userId = 1;

$chatReply->deleteReply($replyId, $userId);
```