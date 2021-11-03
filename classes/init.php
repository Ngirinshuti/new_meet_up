<?php
// intialization file
require_once __DIR__ . "/../auth/authenticate.php";

require_once  __DIR__ . "/../config.php";
require_once __DIR__ . '/./Session.php';
require_once __DIR__ . '/./old_user.php';
require_once __DIR__ . '/./DB.php';
require_once __DIR__ . '/./message.php';
require_once __DIR__ . '/./date.php';
require_once __DIR__ . '/../auth/Auth.php';

$db_connection  = DB::conn();
$user_obj       = new OldUser($db_connection, $me->username);
$msg_obj        = new Message($db_connection, $me->username);
$date_obj       = new Dates();

$users          = $user_obj->get_all_users(true);
$users_num      = $user_obj->get_all_users(false);
$unread         = $msg_obj->get_all_unread();

Session::clearSeen();

if (Session::has("url.current")) {
    Session::set("url.last", Session::get('url.current'));
    Session::set("url.last.full", Session::get('url.current.full'));
} 

Session::set("url.current", current_url());
Session::set("url.current.full", current_url_full());


