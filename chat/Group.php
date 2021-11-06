<?php

require_once __DIR__ . '/Chat.php';
require_once __DIR__ . '/Message.php';


class Group implements GroupInterface
{
    public int $id;
    public string $name;

    public static function create(string $name): Group
    {
        $conn = DB::conn();
        $query = "INSERT INTO `groups` (`name`) VALUES(?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$name]);

        return Group::findOne($conn->lastInsertId("id"));
    }


    public static function findOne(int $id): Group|bool
    {
        $query = "SELECT * FROM `groups` WHERE `id` = ?";
        $stmt = DB::conn()->prepare($query);
        $stmt->execute([$id]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        return $stmt->fetch();
    }

    public function join(string $username, string $role = "member"): bool
    {
        $query = "INSERT INTO `user_groups` (`username`, `group_id`, `role`)
            VALUES (:username, :group_id, :role)
        ";

        $stmt = DB::conn()->prepare($query);
        $successs = $stmt->execute([":username" => $username, ":group_id" => $this->id, ":role" => $role]);

        return boolval($successs);
    }


    public function leave(string $username): bool
    {
        $query = "DELETE FROM `groups` WHERE `username` ? AND `group_id` = ?";
        $stmt = DB::conn()->prepare($query);
        $success = $stmt->execute([$username, $this->id]);

        return boolval($success);
    }

    public function getMessages(): array
    {
        $query = "SELECT * FROM 
        `messages` WHERE `group_id` = ?
        ORDER BY `created_at` DESC
        ";

        $stmt = DB::conn()->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Message::class);
        $stmt->execute([$this->id]);

        return $stmt->fetchAll();
    }

    public function getMembers(): array
    {
        $query = "SELECT `users`.* FROM `user_groups`
        JOIN `users` ON `users`.`username` = `user_groups`.`username`
        WHERE `group_id` = ?
        ";
        $stmt = DB::conn()->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $stmt->execute([$this->id]);

        return $stmt->fetchAll();
    }


    public function isMember(string $username): bool
    {
        $query = "SELECT * FROM `user_groups` WHERE `username` = ? AND `group_id` = ?";
        $stmt = DB::conn()->prepare($query);
        $stmt->execute([$username, $this->id]);
        
        return boolval($stmt->rowCount());
    }

}
