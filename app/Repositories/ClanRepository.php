<?php


class ClanRepository
{

    private PDO $db;



    public function __construct()
    {

        $this->db =
            database()
                ->getConnection();

    }




    /**
     * Проверка существования клана
     */
    public function exists($tag): bool
    {

        $tag = normalizeTag($tag);


        $stmt =
            $this->db->prepare(
                "
                SELECT 1

                FROM clans

                WHERE tag = ?

                LIMIT 1
                "
            );


        $stmt->execute([
            $tag
        ]);


        return (bool)$stmt->fetchColumn();

    }





    /**
     * Добавление клана
     */
    public function create(array $clan): bool
    {

        $stmt =
            $this->db->prepare(
                "
                INSERT INTO clans
                (
                    tag,
                    name,
                    level,
                    members,
                    war_league,
                    capital_league,
                    last_sync
                )

                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW()
                )
                "
            );


        return $stmt->execute([

            normalizeTag($clan['tag']),

            $clan['name'],

            $clan['clanLevel'],

            $clan['members'],

            $clan['warLeague']['name']
                ?? '',

            $clan['capitalLeague']['name']
                ?? ''

        ]);

    }





    /**
     * Получить список кланов
     */
    public function all(): array
    {

        $stmt =
            $this->db->query(
                "
                SELECT tag, name

                FROM clans

                ORDER BY name
                "
            );


        return $stmt->fetchAll();

    }







    public function getTags(): array
    {
        $stmt =
            $this->db->query("

                SELECT tag

                FROM clans

            ");


        return $stmt->fetchAll();
    }


}