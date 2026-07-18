<?php


class UserPlayerRepository
{


    private PDO $db;



    public function __construct()
    {
        $this->db =
            database()->getConnection();
    }





    // =====================================
    // ПОЛУЧИТЬ ВСЕ АККАУНТЫ ПОЛЬЗОВАТЕЛЯ
    // =====================================

    public function getByUser(
        int $telegram_id
    ): array
    {

        $stmt = $this->db->prepare("

            SELECT *

            FROM user_players

            WHERE telegram_id = ?

            ORDER BY is_main DESC, verified_at ASC

        ");


        $stmt->execute([
            $telegram_id
        ]);


        return $stmt->fetchAll();

    }





    // =====================================
    // ПОЛУЧИТЬ ОСНОВНОЙ АККАУНТ
    // =====================================

    public function getMain(
        int $telegram_id
    ): ?array
    {

        $stmt = $this->db->prepare("

            SELECT *

            FROM user_players

            WHERE telegram_id = ?

            AND is_main = 1

            LIMIT 1

        ");


        $stmt->execute([
            $telegram_id
        ]);


        $result =
            $stmt->fetch();


        return $result ?: null;

    }





    // =====================================
    // ПРОВЕРКА ПРИВЯЗКИ АККАУНТА
    // =====================================

    public function exists(
        string $player_tag
    ): bool
    {

        $player_tag =
            normalizeTag($player_tag);



        $stmt = $this->db->prepare("

            SELECT player_tag

            FROM user_players

            WHERE player_tag = ?

            LIMIT 1

        ");



        $stmt->execute([
            $player_tag
        ]);


        return (bool)$stmt->fetchColumn();

    }





    // =====================================
    // ДОБАВИТЬ АККАУНТ
    // =====================================

    public function create(
        int $telegram_id,
        string $player_tag
    ): bool
    {

        $player_tag =
            normalizeTag($player_tag);



        if ($this->exists($player_tag)) {

            return false;

        }



        $stmt = $this->db->prepare("

            SELECT COUNT(*)

            FROM user_players

            WHERE telegram_id = ?

        ");



        $stmt->execute([
            $telegram_id
        ]);



        $count =
            $stmt->fetchColumn();



        $is_main =
            ($count == 0) ? 1 : 0;




        $stmt = $this->db->prepare("

            INSERT INTO user_players
            (
                player_tag,
                telegram_id,
                is_main,
                verified_at
            )

            VALUES
            (
                ?,
                ?,
                ?,
                NOW()
            )

        ");



        return $stmt->execute([

            $player_tag,

            $telegram_id,

            $is_main

        ]);

    }





    // =====================================
    // СДЕЛАТЬ ОСНОВНЫМ
    // =====================================

    public function setMain(
        int $telegram_id,
        string $player_tag
    ): bool
    {

        $player_tag =
            normalizeTag($player_tag);



        try {

            $this->db->beginTransaction();



            $stmt = $this->db->prepare("

                UPDATE user_players

                SET is_main = 0

                WHERE telegram_id = ?

            ");



            $stmt->execute([
                $telegram_id
            ]);





            $stmt = $this->db->prepare("

                UPDATE user_players

                SET is_main = 1

                WHERE telegram_id = ?

                AND player_tag = ?

            ");



            $stmt->execute([

                $telegram_id,

                $player_tag

            ]);



            $this->db->commit();



            return true;


        }
        catch(Exception $e)
        {

            if ($this->db->inTransaction()) {

                $this->db->rollBack();

            }


            return false;

        }

    }


}