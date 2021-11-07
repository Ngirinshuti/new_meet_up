<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/Group.php';
require_once __DIR__ . '/../forms/Validator.php';

$group = isset($_GET['group']) ? Group::findOne($_GET['group']) : null;

$validator = new Validator();

list($errors, $data, $errorClass, $mainError, $msg, $csrf) = $validator->helpers();


$groups = !$group ? Group::getUserGroups($me->username) : [];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo getUrl("/css/chat_groups.css"); ?>">
    <link rel="stylesheet" href="<?php echo getUrl("/css/chat.css"); ?>">
    <title>Groups</title>
</head>

<body>
    <div class="container">
        <?php require_once __DIR__ . '/../menu/menu.php'; ?>

        <div class="chatContainer">
            <header class="chatHeader">
                <a href="<?php echo getBackUrl(); ?>" class="btn btn-icon"><i class="fa fa-arrow-left"></i></a>
                <h4 class="title"><?php echo $group ? $group->name : "Groups" ?></h4>
                <?php if ($group) : ?>
                    <a class="btn" href="<?php echo getUrl("/chat/chat_groups.php") ?>">
                        ALL Groups
                    </a>
                <?php else : ?>
                    <a class="btn" href="<?php echo getUrl("/chat/chat_friends.php?create") ?>" data-group-create>
                        <i class="fa fa-group"></i>
                        Create Group
                    </a>
                <?php endif; ?>
            </header>

            <?php echo $msg() ?>
            <?php echo $mainError(); ?>


            <?php if (!$group) : ?>
                <div class="chatGroupsContainer">
                    <?php if (!count($groups)) : ?>
                        <p class="centered" style="text-align: center;">You don't belong to any group yet.</p>
                    <?php endif ?>
                    <h3 class="">Your groups</h3>
                    <div class="chatGroupList">
                        <?php foreach ($groups as $group) : ?>
                            <div class="chatGroup">
                                <a href="<?php echo getUrl("/chat/chat_groups.php?group={$group->id}") ?>" class="chatGroupUserName"><?php echo $group->name ?></a>
                                <div class="chatGroupBtns">
                                    <a href="#" class="btn">Leave</a>
                                    <a href="<?php echo getUrl("/chat/chat_room.php?group={$group->id}") ?>" class="btn">Messages</a>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

            <?php else : ?>
                <div class="groupProfile">
                    <div class="groupImg">
                        <?php echo $group->name[0]; ?>
                    </div>
                    <div class="groupName"><?php echo $group->name ?></div>
                    <a href="<?php echo getUrl("/chat/chat_room.php?group=" . $group->id) ?>" class="btn groupChatBtn">Chat</a>
                </div>
                <h5 class="groupTitle">Members</h5>
                <div class="groupMembers">
                    <?php foreach ($group->getMembers() as $member) : ?>
                        <div class="groupMember">
                            <div class="groupMemberImg chatUserImg">
                                <img src="<?php echo getUrl("/images/{$member->profile_pic}") ?>" alt="p">
                            </div>
                            <div class="groupMemberUserName">
                                <?php echo $member->username; ?>
                            </div>
                            <div class="groupMemberRole">
                                <?php echo $member->role; ?>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>
    </div>

</body>

</html>