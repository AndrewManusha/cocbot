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
        int $telegramId,
        string $playerTag,
        array $labels
    ): bool
    {

        $playerTag =
            normalizeTag($playerTag);


        $labels =
            implode(
                ',',
                $labels['ids']
            );



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
                    DATE_ADD(NOW(), INTERVAL 5 MINUTE)
                )


                ON DUPLICATE KEY UPDATE

                    telegram_id = VALUES(telegram_id),
                    labels = VALUES(labels),
                    expires_at =
                        DATE_ADD(
                            NOW(),
                            INTERVAL 5 MINUTE
                        )

            ");


        return $stmt->execute([

            $playerTag,
            $telegramId,
            $labels

        ]);

    }



    // =====================================
    // СОХРАНИТЬ MESSAGE DATA
    // =====================================

    public function setMessage(
        string $playerTag,
        int $chatId,
        int $messageId
    ): bool
    {

        $stmt =
            $this->db->prepare("

                UPDATE player_verifications

                SET

                    chat_id = ?,

                    message_id = ?

                WHERE player_tag = ?

            ");


        return $stmt->execute([

            $chatId,
            $messageId,
            normalizeTag($playerTag)

        ]);

    }



    // =====================================
    // ПОЛУЧИТЬ АКТИВНУЮ ПРОВЕРКУ
    // =====================================

    public function find(
        string $playerTag
    ): ?array
    {

        $stmt =
            $this->db->prepare("

                SELECT *

                FROM player_verifications

                WHERE player_tag = ?

                AND expires_at >= NOW()

                LIMIT 1

            ");



        $stmt->execute([

            normalizeTag($playerTag)

        ]);



        $result =
            $stmt->fetch();



        return $result ?: null;

    }



    // =====================================
    // УДАЛИТЬ ПРОВЕРКУ
    // =====================================

    public function delete(
        string $playerTag
    ): bool
    {

        $stmt =
            $this->db->prepare("

                DELETE FROM player_verifications

                WHERE player_tag = ?

            ");



        return $stmt->execute([

            normalizeTag($playerTag)

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