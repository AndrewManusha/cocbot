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




    // =====================================
    // ПРОВЕРКА АДМИНА
    // =====================================

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





    // =====================================
    // ПОЛУЧИТЬ ВСЕХ АДМИНОВ
    // =====================================

    public function all(): array
    {

        $stmt =
            $this->db->query(
                "

                SELECT users.*

                FROM users

                INNER JOIN admins

                ON users.telegram_id = admins.telegram_id

                ORDER BY username

                "
            );


        return $stmt->fetchAll();

    }


}