[![StyleCI](https://github.styleci.io/repos/126861813/shield?branch=master)](https://github.styleci.io/repos/126861813)

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

The `createChat` method in ChatManager takes two arguments. The first is the creator of the chat, and the second is an array of the recipients (the creator must be included).

```php
$creator = User::find(1);
$anotherUser = User::find(2);
$recipients = [$creator, $anotherUser];

$chat = $chatManager->createChat($creator, $recipients);
```

##### Add Users To An Existing Chat

In order to add new recipients to an existing chat, just pass the chat object and the user.

```php
$chat = Chat::find(1);
$user = User::find(3);

$chatManager->addUserToChat($chat, $user)
```

### Get A Chat

The `getChat` method takes the logged in `userId` since each user can delete replies on his part while not affecting other users involved in the chat.

```php
$chat = Chat::find(1);
$user = User::find(3);

$chat = $chatRepository->getChat($chat, $user);
```

If you want to get the replies along with the chat call the `getChatWithReplies` method with the same parameters as in the `getChat` method.

### Get Chats

To get the chats of a specific user:


```php
$user = User::find(1);

$chats = $chatRepository->getChats($user);
```

You can limit the results by passing `limit` and `offset` params.

### Delete A Chat

When a user deletes a chat, other users involved in the chat are not affected. What really happens is that the relationship created for that user in the `chat_user` table gets deleted.

```php
$chat = Chat::find(1);

$chatManager->deleteChat($chat);
```

### Create A Reply

In order to create a reply you need to pass the `chat`, `user` & the `text` of the reply respectively.

```php
$chat = Chat::find(1);
$user = User::find(1);

$chatManager->createReply($chat, $user, "Hello World!");
```

### Get Replies

The `userId` here is used to get the replies form the user's point of view (to avoid fetching replies that the user deleted). 

```php
$chat = Chat::find(1);
$user = User::find(1);

$replies = $chatRepository->getReplies($chat, $user);
```

You can also limit the replies by passing `limit` and `offset`.

### Get New Replies

You can use this method in a real-time chat to fetch new replies using a time marker.

```php
$chat = Chat::find(1);
$user = User::find(1);

$timeMarker = \Carbon\Carbon::now();

$newReplies = $chatRepository->getNewReplies($chat, $user, $timeMarker);
```

### Delete A Reply

Once again, this won't delete the actual reply stored in `chat_replies` table. It just deletes the relationship for the user with this reply. The reply only gets deleted if there are no users interested in this reply.

```php
$reply = ChatReply::find(1);
$user = User::find(1);

$chatManager->deleteReply($reply, $user);
```
