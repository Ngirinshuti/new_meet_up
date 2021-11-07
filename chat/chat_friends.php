<?php
require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../classes/friend_system.php';
require_once __DIR__ . '/../forms/Validator.php';
require_once __DIR__ . '/Group.php';


$friends = (new Friends($db_connection, $me->username))->getFriendsSorted();

$validator = new Validator();

$validator->methodPost(function (Validator &$validator) {
    $validator->addData($_POST)->addRules([
        'name' => ['not_empty' => true],
    ])->validate();

    if (!isset($_POST['members']) || !count($_POST['members'])) {
        $validator->setMainError("No members selected!")->saveMainError()
            ->redirect(current_url_full());
    } else {
        $validator->isValid(function (Validator $validator) {
            $members = $_POST['members'];
            $name = $validator->valid_data['name'];

            $group = Group::create($name);
            $group->join(Auth::currentUser()->username, "admin");
            foreach ($members as $member) {
                $group->join($member);
            }
            $validator->setSuccessMsg("Goup was created successfully with " . count($members) + 1 . " members! You are admin.")
                ->redirect(current_url());
        });
    }

    $validator->isInvalid(function (Validator $validator) {
        $validator->setMainError("Correct errors below")->redirect(current_url_full());
    });
});


list($errors, $data, $errorClass, $mainError, $msg, $csrf) = $validator->helpers();

function in_members(string $username)
{
    return isset($_POST['members']) && in_array($username, $_POST['members'], true);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="<?php echo getUrl("/css/chat.css") ?>">
    <link rel="stylesheet" href="<?php echo getUrl("/css/chat-friends.css") ?>">
    <title>Chat - Groups</title>
</head>

<body>
    <div class="container">
        <?php require_once __DIR__ . '/../menu/menu.php'; ?>
        <div class="chatContainer">
            <header class="chatHeader">
                <a href="<?php echo getUrl("/chat/index.php") ?>" class="btn btn-icon"><i class="fa fa-arrow-left"></i></a>
                <h4 class="title">Friends</h4>
                <?php if (isset($_GET['create'])) : ?>
                    <a class="btn" href="<?php echo getUrl("/chat/chat_friends.php") ?>" data-group-create>
                        <i class="fa fa-close"></i>
                        Cancel Create
                    </a>
                <?php endif; ?>
                <a href="<?php echo getUrl("/chat/chat_groups.php"); ?>" class="btn">Groups</a>
            </header>
            <?php echo $msg() ?>
            <?php echo $mainError(); ?>
            <?php if (isset($_GET['create'])) : ?>
                <div class="mainFormContainer groupFormContainer">
                    <form id="group-create-form" action="" class="mainForm groupForm" method="POST">
                        <?php echo $csrf(); ?>
                        <div class="formHeader">
                        </div>
                        <div class="formBody">
                            <div class="mainInput <?php echo $errorClass('name'); ?>">
                                <label for="group_name">Group Name</label>
                                <input value="<?php echo $data('name') ?>" placeholder="Enter group name.." type="text" name="name" id="group_name">
                                <?php echo $errors('name') ?>
                            </div>
                            <button form="group-create-form" style="display: inline-block; margin-bottom: 2rem;" type="submit">Create</button>
                        </div>
                        <h4 class="formText">Choose friends to start a group</h4>
                    </form>
                </div>
            <?php else : ?>
                <div class="chatFriendsContainer">
                    <div class="chatFriendsSearch">
                        <label for="search">
                            <i class="fa fa-search"></i>
                        </label>
                        <input data-friends-search-input placeholder="Search friends" type="search" name="search" id="search">
                    </div>
                <?php endif ?>

                <?php if (!count($friends)) : ?>
                    <p class="centered" style="text-align: center;">You have no friends</p>
                <?php endif ?>
                <div class="chatFriendsList">
                    <?php foreach ($friends as $friend) : ?>
                        <div class="chatFriend <?php echo isset($_GET['create']) ? "selecting" : "" ?> <?php echo $friend->status === 'online' ? 'active' : ''; ?>">
                            <div class="chatUserImg">
                                <img src="<?php echo getUrl("/images/{$friend->profile_pic}") ?>" alt="profile">
                            </div>
                            <a href="#" class="chatFriendUserName"><?php echo $friend->username ?></a>
                            <?php if ($friend->status === 'offline') : ?>
                                <div class="chatFriendLastSeen">
                                    last seen: <?php echo  $date_obj->dateDiffStr($friend->last_seen); ?>
                                </div>
                            <?php endif ?>
                            <?php if (isset($_GET['create'])) : ?>
                                <div class="chatFriendSelect">
                                    <input <?php echo in_members($friend->username) ? "checked" : ""; ?> form="group-create-form" type="checkbox" name="members[]" value="<?php echo $friend->username ?>">
                                </div>
                            <?php endif; ?>
                            <a href="<?php echo getUrl("/chat/chat_room.php?user={$friend->username}") ?>" class="chatFriendLink"></a>
                        </div>

                    <?php endforeach ?>
                    <template>
                        <div class="chatFriend">
                            <div class="chatUserImg">
                                <img src="../images/default.png" alt="profile">
                            </div>
                            <a href="#" class="chatFriendUserName">Jane Doe</a>
                            <div class="chatFriendSelect">
                                <input type="checkbox" name="select_friend">
                            </div>
                        </div>
                    </template>
                </div>
                </div>
        </div>

    </div>
    <script src="<?php echo getUrl("/js/chat_friends.js") ?>" defer></script>
</body>

</html>