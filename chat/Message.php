<?php


require_once __DIR__ . "/../classes/DB.php";
require_once __DIR__ . "/Chat.php";

/**
 * Message class
 */
class Message  implements MessageInterface
{
    public int    $id;
    public string $body;
    public string $status;
    public string $sender;
    public string $reciever;
    public ?int    $story_id;
    public string $created_at;
    public ?int    $group_id;


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


    public static function getConversation(string $user_1, string $user_2): array
    {
        $query = "SELECT messages.*,`users`.`profile_pic` FROM `messages` 
            JOIN `users` ON `users`.`username` = messages.`sender`
            WHERE :user_1 IN (`sender`,`reciever`) AND :user_2 IN (`sender`,`reciever`)
            ORDER BY `created_at` ASC
        ";

        $stmt = DB::conn()->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([":user_1" => $user_1, ":user_2" => $user_2]);

        return $stmt->fetchAll();
    }



    public static function getUserRecentMessages(string $reciever): array
    {
        $query = "SELECT 
            IF(m.sender = :reciever, 'me', m.sender) as `from`,
            IF(m.reciever = :reciever, 'me', m.reciever) as `to`,
            (
                SELECT COUNT(messages.id) FROM messages WHERE status = 'unread' 
                AND messages.reciever = :reciever
                AND  messages.sender = IF(m.sender = :reciever, m.reciever, m.sender)
            ) as unread_count,
            m.*,users.profile_pic
            FROM messages m 
            JOIN `users` ON `users`.`username` = IF(m.sender = :reciever, m.reciever, m.sender)
            WHERE m.date_ IN (
                SELECT 
                MAX(ms.date_) as `date` 
                FROM messages ms
                WHERE :reciever IN (ms.sender,ms.reciever)
                AND m.sender = ms.sender
                GROUP BY IF(ms.sender = :reciever, ms.reciever, ms.sender)
                ORDER BY `date` DESC
            )
            GROUP BY IF(m.sender = :reciever, m.reciever, m.sender)
            ORDER BY m.date_ DESC
        ";


        $stmt = DB::conn()->prepare($query);

        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([":reciever" => $reciever]);

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
