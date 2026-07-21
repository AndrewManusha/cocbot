<?php


class PlayerRepository
{

    private PDO $db;



    public function __construct()
    {
        $this->db =
            database()
                ->getConnection();
    }



    // =====================================
    // ПРОВЕРКА СУЩЕСТВОВАНИЯ ИГРОКА
    // =====================================

    public function exists(
        string $playerTag
    ): bool
    {
        $playerTag =
            normalizeTag(
                $playerTag
            );


        $stmt =
            $this->db->prepare(
                "
                SELECT player_tag

                FROM players

                WHERE player_tag = ?

                LIMIT 1
                "
            );


        $stmt->execute([
            $playerTag
        ]);


        return (bool)$stmt->fetchColumn();
    }



    // =====================================
    // СОХРАНИТЬ УЧАСТНИКА КЛАНА
    // =====================================

    public function sync(
        array $player,
        string $clanTag
    ): bool
    {
        $playerTag =
            normalizeTag(
                $player['tag']
                ??
                ''
            );


        $clanTag =
            normalizeTag(
                $clanTag
            );


        if (
            $playerTag === ''
            ||
            $clanTag === ''
        ) {

            return false;
        }



        $stmt =
            $this->db->prepare(
                "
                INSERT INTO players
                (
                    player_tag,
                    clan_tag,

                    name,
                    role,
                    league,

                    town_hall_level,
                    exp_level,

                    war_stars,

                    donations,
                    donations_received,

                    clan_capital_contributions,

                    last_sync
                )

                VALUES
                (
                    :player_tag,
                    :clan_tag,

                    :name,
                    :role,
                    :league,

                    :town_hall_level,
                    :exp_level,

                    :war_stars,

                    :donations,
                    :donations_received,

                    :clan_capital_contributions,

                    NOW()
                )


                ON DUPLICATE KEY UPDATE


                    clan_tag =
                        VALUES(clan_tag),


                    name =
                        VALUES(name),


                    role =
                        VALUES(role),


                    league =
                        VALUES(league),


                    town_hall_level =
                        VALUES(town_hall_level),


                    exp_level =
                        VALUES(exp_level),


                    war_stars =
                        VALUES(war_stars),


                    donations =
                        VALUES(donations),


                    donations_received =
                        VALUES(donations_received),


                    clan_capital_contributions =
                        VALUES(clan_capital_contributions),


                    last_sync =
                        NOW()

                "
            );



        return $stmt->execute([

            'player_tag' =>
                $playerTag,


            'clan_tag' =>
                $clanTag,


            'name' =>
                $player['name']
                ??
                '',


            'role' =>
                $player['role']
                ??
                '',


            'league' =>
                $player['leagueTier']['name']
                ??
                '',


            'town_hall_level' =>
                $player['townHallLevel']
                ??
                0,


            'exp_level' =>
                $player['expLevel']
                ??
                0,


            'war_stars' =>
                $player['warStars']
                ??
                0,


            'donations' =>
                $player['donations']
                ??
                0,


            'donations_received' =>
                $player['donationsReceived']
                ??
                0,


            'clan_capital_contributions' =>
                $player['clanCapitalContributions']
                ??
                0

        ]);
    }



    // =====================================
    // ПОЛУЧИТЬ ИГРОКА
    // =====================================

    public function find(
        string $playerTag
    ): ?array
    {
        $stmt =
            $this->db->prepare(
                "
                SELECT *

                FROM players

                WHERE player_tag = ?

                LIMIT 1
                "
            );


        $stmt->execute([
            normalizeTag($playerTag)
        ]);


        $player =
            $stmt->fetch();


        return $player ?: null;
    }



    // =====================================
    // ПОЛУЧИТЬ ИГРОКОВ КЛАНА
    // =====================================

    public function getByClan(
        string $clanTag
    ): array
    {
        $stmt =
            $this->db->prepare(
                "
                SELECT *

                FROM players

                WHERE clan_tag = ?

                ORDER BY name
                "
            );


        $stmt->execute([
            normalizeTag($clanTag)
        ]);


        return $stmt->fetchAll();
    }



}