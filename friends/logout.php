<?php 
// logout file

require_once __DIR__ . "/../classes/init.php";

$me->setProperty("status", "offline");

Auth::logout();

header("Location: $ROOT_URL/index.php");

exit("Logged out");