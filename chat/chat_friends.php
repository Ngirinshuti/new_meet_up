<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/css/font-awesome-4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/chat.css">
    <link rel="stylesheet" href="../css/chat-friends.css">
    <title>Chat - Groups</title>
</head>

<body>
    <div class="container">
        <div class="chatContainer">
            <header class="chatHeader">
                <h1 class="title">Friends</h1>
                <a href="./index.html" class="btn">Recents</a>
                <button>Create Group</button>
            </header>
            <div class="chatFriendsContainer">
                <div class="chatFriendsSearch">
                    <label for="search">
                        <i class="fa fa-search"></i>
                    </label>
                    <input placeholder="Search friends" type="search" name="search" id="search">
                </div>
                <div class="chatFriendsList">
                    <div class="chatFriend selecting">
                        <div class="chatUserImg">
                            <img src="../images/default.png" alt="profile">
                        </div>
                        <a href="#" class="chatFriendUserName">Jane Doe</a>
                        <div class="chatFriendSelect">
                            <!-- <label for="select_friend">Select</label> -->
                            <input type="checkbox" name="select_friend" id="select_friend">
                            <label for="select_friend" class="fakeInput"></label>
                        </div>
                    </div>
                    <div class="chatFriend">
                        <div class="chatUserImg">
                            <img src="../images/default.png" alt="profile">
                        </div>
                        <a href="#" class="chatFriendUserName">Jane Doe</a>
                        <div class="chatFriendSelect">
                            <input type="checkbox" name="select_friend">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>