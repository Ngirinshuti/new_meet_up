<?php


require __DIR__ . "/../classes/DB.php";
require __DIR__ . "/Chat.php";

/**
 * Message class
 */
class Message  implements MessageInterface
{
    public static function create(
        string $sender,
        string $reciever,
        string $body,
        ?int $story_id,
        ?int $group_id
    ): Message|bool {
        $conn = DB::conn();
        $query = "INSERT INTO `messages` 
            (`sender`, `reciever`, `body`, `story_id`, `group_id`)
            VALUES (:sender, :reciever, :body, :story_id, :group_id)
        ";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            ":sender" => $sender,
            ":reciever" => $reciever,
            ":body" => $body,
            ":story_id" => $story_id,
            ":group_id" => $group_id
        ]);

        $lastId = $conn->lastInsertId("id");

        return Message::findOne($lastId);
    }

    public static function findOne(int $id): Message|bool
    {
        $query = "SELECT * FROM `messages` WHERE `id` = ?";
        $stmt = DB::conn()->prepare($query);
        $stmt->execute([$id]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        return $stmt->fetch();
    }


    public static function getConversation(string $sender, string $reciever): array
    {
        $query = "SELECT * FROM `messages` 
            WHERE `sender` = :sender AND `reciever` = :reciever 
            ORDER BY `created_at` DESC
        ";

        $stmt = DB::conn()->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([":sender" => $sender, ":reciever" => $reciever]);

        return $stmt->fetchAll();
    }



    public static function getUserRecentMessages(string $reciever): array
    {
        $query = "SELECT `users`.*, `messages`.*, `groups`.`name` 
            FROM `messages` 
            JOIN `users` ON `users`.`username` = `messages`.`sender`
            JOIN `groups` ON `groups`.`id` = `messages`.`group_id`
            WHERE `reciever` = ?
            GROUP BY `sender`
            ORDER BY `created_at` DESC
        ";

        $stmt = DB::conn()->prepare($query);

        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([$reciever]);

        return $stmt->fetchAll();
    }


    public function getStory(): ?object
    {
        $query = "SELECT * FROM `stories` WHERE `id` = ?";
        $stmt = DB::conn()->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Story::class);
        $stmt->execute([$this->story_id]);
        $story = $stmt->fetch();
        return $story;
    }


    public function getReciever(): User
    {
        $query = "SELECT * FROM `users` WHERE `username` = ?";
        $stmt = DB::conn()->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([$this->reciever]);

        return $stmt->fetch();
    }

    public function getGroup(): ?object
    {
        $query = "SELECT * FROM `groups` WHERE `id` = ?";
        $stmt = DB::conn()->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([$this->group_id]);

        return $stmt->fetch();
    }
    

    public function getSender(): User
    {
        $query = "SELECT * FROM `users` WHERE `username` = ?";
        $stmt = DB::conn()->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([$this->sender]);
    
        return $stmt->fetch();
    }

    public function setStatus(int $id, string $status): Message|bool
    {
        $query = "UPDATE `messages` SET `status` = ? WHERE `id` = ?";
        
        
        $stmt = DB::conn()->prepare($query);
        $stmt->execute([$status, $id]);
        return Message::findOne($id);
    }
}
