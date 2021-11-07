<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../forms/Validator.php';

$validator = new Validator();


$validator->methodPost(function (Validator $validator) {
    var_dump($_POST);
    
    exit;
    $validator->addData($_POST)->addRules([
        'name' => ['not_empty' => true],
        ])->validate();

    if (!isset($_POST['members']) || !count($_POST['members'])) {
        $validator->setMainError("No members selected!");
        $validator->redirect(getUrl("/chat/chat_friends.php?create"));
    }

    $validator->isInvalid(function (Validator $validator) {
        $validator->setMainError("Something went wrong!");
        $validator->redirect(getUrl("/chat/chat_friends.php?create"));
    });

    $validator->isValid(function (Validator $validator) {
        $validator->setSuccessMsg("Group {$_POST['name']} created!");
        $validator->redirect(getUrl("/chat/chat_friends.php?create"));
    });
});


