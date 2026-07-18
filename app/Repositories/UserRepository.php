<?php


class UserRepository
{


    private PDO $db;



    public function __construct()
    {

        $this->db =
            database()->getConnection();

    }





    // =====================================
    // ПОЛУЧИТЬ ПОЛЬЗОВАТЕЛЯ
    // =====================================

    public function find($telegram_id)
    {

        $stmt =
            $this->db->prepare("

                SELECT *

                FROM users

                WHERE telegram_id = ?

                LIMIT 1

            ");



        $stmt->execute([

            $telegram_id

        ]);



        return $stmt->fetch();

    }







    // =====================================
    // ДОБАВИТЬ ПОЛЬЗОВАТЕЛЯ
    // =====================================

    public function create(array $data): bool
    {

        $stmt =
            $this->db->prepare("

                INSERT INTO users
                (
                    telegram_id,
                    username,
                    first_name,
                    last_name,
                    joined_at,
                    last_activity
                )

                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW(),
                    NOW()
                )

            ");



        return $stmt->execute([


            $data['telegram_id'],


            $data['username'] ?? '',


            $data['first_name'] ?? '',


            $data['last_name'] ?? ''


        ]);

    }







    // =====================================
    // ОБНОВИТЬ АКТИВНОСТЬ
    // =====================================

    public function updateActivity($telegram_id): bool
    {

        $stmt =
            $this->db->prepare("

                UPDATE users

                SET last_activity = NOW()

                WHERE telegram_id = ?

            ");



        return $stmt->execute([

            $telegram_id

        ]);

    }







    // =====================================
    // ПОЛУЧИТЬ ВСЕХ ПОЛЬЗОВАТЕЛЕЙ
    // =====================================

    public function all(): array
    {

        $stmt =
            $this->db->query("

                SELECT *

                FROM users

                ORDER BY joined_at ASC

            ");



        return $stmt->fetchAll();

    }







    // =====================================
    // ПОЛЬЗОВАТЕЛИ БЕЗ АДМИНОВ
    // =====================================

    public function allWithoutAdmins(): array
    {

        $stmt =
            $this->db->query("

                SELECT users.*

                FROM users

                LEFT JOIN admins

                ON users.telegram_id = admins.telegram_id

                WHERE admins.telegram_id IS NULL

                ORDER BY username

            ");



        return $stmt->fetchAll();

    }




    


    // =====================================
    // ПОЛЬЗОВАТЕЛИ АДМИНЫ
    // =====================================

    public function allAdmins(): array
    {

        $stmt =
            $this->db->query("

                SELECT users.*

                FROM users

                INNER JOIN admins

                ON users.telegram_id = admins.telegram_id

                ORDER BY username

            ");



        return $stmt->fetchAll();

    }


}