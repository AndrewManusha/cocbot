<?php


class PlayerRepository
{

    private PDO $db;


    public function __construct()
    {

        $this->db =
            database()->getConnection();

    }





    // =====================================
    // ПРОВЕРКА СУЩЕСТВОВАНИЯ ИГРОКА
    // =====================================

    public function exists(string $playerTag): bool
    {

        $playerTag =
            $this->normalizeTag(
                $playerTag
            );


        $stmt =
            $this->db->prepare("

                SELECT player_tag

                FROM players

                WHERE player_tag = ?

                LIMIT 1

            ");


        $stmt->execute([

            $playerTag

        ]);


        return (bool)$stmt->fetchColumn();

    }




    // =====================================
    // СОХРАНИТЬ ТЕГ ПОЛЬЗОВАТЕЛЯ
    // =====================================

    public function setUserTag(
        int $telegramId,
        string $playerTag
    ): bool
    {

        $playerTag =
            $this->normalizeTag(
                $playerTag
            );



        if (
            !$this->checkPlayerExistsInClan(
                $playerTag
            )
        ) {

            return false;

        }



        $stmt =
            $this->db->prepare("

                UPDATE users

                SET player_tag = ?

                WHERE telegram_id = ?

            ");



        return $stmt->execute([

            $playerTag,

            $telegramId

        ]);

    }




    // =====================================
    // НОРМАЛИЗАЦИЯ TAG
    // =====================================

    private function normalizeTag(
        string $tag
    ): string
    {

        return strtoupper(

            trim(

                ltrim(
                    $tag,
                    '#'
                )

            )

        );

    }




    // =====================================
    // ПРОВЕРКА ИГРОКА В КЛАНЕ
    // =====================================
    // Временно оставляем как было.
    // Потом заменим на ClanMemberRepository.

    private function checkPlayerExistsInClan(
        string $playerTag
    ): bool
    {

        return true;

    }


}