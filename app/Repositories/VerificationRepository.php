<?php


class VerificationRepository
{


    private PDO $db;



    public function __construct()
    {
        $this->db =
            database()->getConnection();
    }







    // =====================================
    // СОЗДАТЬ / ОБНОВИТЬ ПРОВЕРКУ
    // =====================================

    public function create(
        int $telegram_id,
        string $player_tag,
        array $labels
    ): bool
    {

        $player_tag =
            normalizeTag(
                $player_tag
            );



        if (
            isset(
                $labels['ids']
            )
        ) {

            $labels =
                implode(
                    ',',
                    $labels['ids']
                );

        }
        else {

            $labels =
                implode(
                    ',',
                    $labels
                );

        }






        $stmt =
            $this->db->prepare("

                INSERT INTO player_verifications
                (
                    player_tag,
                    telegram_id,
                    labels,
                    expires_at
                )

                VALUES
                (
                    ?,
                    ?,
                    ?,
                    DATE_ADD(
                        NOW(),
                        INTERVAL 5 MINUTE
                    )
                )


                ON DUPLICATE KEY UPDATE

                    telegram_id = VALUES(telegram_id),

                    labels = VALUES(labels),

                    expires_at =
                        DATE_ADD(
                            NOW(),
                            INTERVAL 5 MINUTE
                        ),

                    chat_id = NULL,

                    message_id = NULL

            ");





        return $stmt->execute([

            $player_tag,

            $telegram_id,

            $labels

        ]);

    }









    // =====================================
    // ПОЛУЧИТЬ АКТИВНУЮ ПРОВЕРКУ
    // =====================================

    public function find(
        string $player_tag
    ): ?array
    {

        $player_tag =
            normalizeTag(
                $player_tag
            );



        $stmt =
            $this->db->prepare("

                SELECT *

                FROM player_verifications

                WHERE player_tag = ?

                AND expires_at >= NOW()

                LIMIT 1

            ");




        $stmt->execute([

            $player_tag

        ]);




        $result =
            $stmt->fetch();



        return $result ?: null;

    }









    // =====================================
    // СОХРАНИТЬ ДАННЫЕ СООБЩЕНИЯ
    // =====================================

    public function setMessage(
        string $player_tag,
        int $chat_id,
        int $message_id
    ): bool
    {

        $player_tag =
            normalizeTag(
                $player_tag
            );



        $stmt =
            $this->db->prepare("

                UPDATE player_verifications

                SET

                    chat_id = ?,

                    message_id = ?

                WHERE player_tag = ?

            ");




        return $stmt->execute([

            $chat_id,

            $message_id,

            $player_tag

        ]);

    }









    // =====================================
    // УДАЛИТЬ ПРОВЕРКУ
    // =====================================

    public function delete(
        string $player_tag
    ): bool
    {

        $player_tag =
            normalizeTag(
                $player_tag
            );



        $stmt =
            $this->db->prepare("

                DELETE FROM player_verifications

                WHERE player_tag = ?

            ");




        return $stmt->execute([

            $player_tag

        ]);

    }









    // =====================================
    // ОЧИСТКА ПРОСРОЧЕННЫХ
    // =====================================

    public function clearExpired(): bool
    {

        $stmt =
            $this->db->prepare("

                DELETE FROM player_verifications

                WHERE expires_at < NOW()

            ");




        return $stmt->execute();

    }


}