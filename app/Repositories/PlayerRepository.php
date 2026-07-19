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

        $playerTag = normalizeTag($playerTag);


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

        $playerTag = normalizeTag($playerTag);



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








    public function sync(array $player, string $clanTag): bool
    {

        $stmt =
            $this->db->prepare("

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
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW()
                )


                ON DUPLICATE KEY UPDATE

                    clan_tag = VALUES(clan_tag),
                    name = VALUES(name),
                    role = VALUES(role),
                    league = VALUES(league),
                    town_hall_level = VALUES(town_hall_level),
                    exp_level = VALUES(exp_level),
                    war_stars = VALUES(war_stars),
                    donations = VALUES(donations),
                    donations_received = VALUES(donations_received),
                    clan_capital_contributions = VALUES(clan_capital_contributions),
                    last_sync = NOW()

            ");



        return $stmt->execute([

            normalizeTag($player['tag']),

            $clanTag,

            $player['name'],

            $player['role'] ?? '',

            $player['leagueTier']['name'] ?? '',

            $player['townHallLevel'] ?? 0,

            $player['expLevel'] ?? 0,

            $player['warStars'] ?? 0,

            $player['donations'] ?? 0,

            $player['donationsReceived'] ?? 0,

            $player['clanCapitalContributions'] ?? 0

        ]);

    }


}