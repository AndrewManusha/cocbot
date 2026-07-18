<?php


class AdminRepository
{

    private PDO $db;



    public function __construct()
    {

        $this->db =
            database()
                ->getConnection();

    }




    public function isAdmin(
        int $telegramId
    ): bool
    {

        $stmt =
            $this->db->prepare(
                "
                SELECT telegram_id

                FROM admins

                WHERE telegram_id = ?

                LIMIT 1
                "
            );


        $stmt->execute([
            $telegramId
        ]);


        return (bool)$stmt->fetch();

    }

}