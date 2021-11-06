<?php

require_once __DIR__ . '/../classes/init.php';

if (!isset($_GET['user']) || !($user = User::findOne($_GET['user']))) {
    header("Location: " . getUrl('/404.php?msg=You should select a user to chat with&url=' . getUrl('/chat/index.php')));
}

if ($user->username === $me->username) {
    exit("Something! Went wrong.");
}

require_once __DIR__ . '/Message.php';

$messages = Message::getConversation($me->username, $user->username);

function shouldCombine(int $i){
    $messages = $GLOBALS['messages'];
    $date_obj = $GLOBALS['date_obj'];

    $prev = isset($messages[$i - 1]) ? $messages[$i - 1] : null;
    $next = isset($messages[$i + 1]) ? $messages[$i + 1] : null;
    $current = $messages[$i];


    if ($next === null) {
        return false;
    }

    // if ($current->sender === $next->sender && $date_obj->dateDiff($next->created_at, $current->created_at) < 1) {
    //     return true;
    // }

    if ($current->sender === $next->sender && $date_obj->underAMinute($next->created_at, $current->created_at)) {
        return true;
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/font-awesome-4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/chat.css">
    <link rel="stylesheet" href="../css/chat-room.css">
    <title>Chat - <?php echo $user->username; ?></title>
</head>

<body>
    <div class="container">
        <?php require_once __DIR__ . '/../menu/menu.php'; ?>
        <div class="chatContainer">
            <header class="chatHeader">
                <a href="<?php echo getUrl("/chat/index.php") ?>" class="btn btn-icon"><i class="fa fa fa-arrow-circle-left"></i></a>
                <h5 class="title"><?php echo $user->fname . " " . $user->lname; ?></h5>
                <div class="headerBtns">
                    <a href="<?php echo getUrl("/chat/chat_friends.php"); ?>" class="btn">Friends</a>
                </div>
            </header>
            <div class="chatRoomContainer">
                <?php if (empty($messages)) : ?>
                    <p style="text-align: center;">You have no chat yet.</p>
                <?php endif; ?>
                <div class="chatMessageList">
                    <?php foreach ($messages as $i => $msg) : ?>
                        <?php if ($i === 0 || !shouldCombine($i-1)) : ?>
                            <div class="chatMessageGroup <?php echo $msg->sender === $me->username ? "sent" : "recieved"; ?>">
                                <div class="chatMessageUser">
                                    <div class="chatUserImg">
                                        <img src="<?php echo getUrl("/images/{$msg->profile_pic}") ?>" alt="profile">
                                    </div>
                                </div>
                            <?php endif ?>
                            <div class="chatMessage">
                                <div class="chatMessageBody"><?php echo $msg->body; ?></div>
                            </div>
                            <?php if (!shouldCombine($i)) : ?>
                                <div class="chatMessageInfo">
                                    <div class="chatMessageTime"><?php echo $date_obj->dateDiffStr($msg->created_at); ?></div>
                                    <div class="chatStatus seen">
                                        <?php echo $msg->status; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="chatRoomInput">
                    <textarea data-chat-input placeholder="Message.." autofocus></textarea>
                    <button data-chat-send class="btn-icon"><i class="fa fa-send"></i></button>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="<?php echo getUrl("/js/chat_room.js") ?>" defer></script>
</body>

</html>