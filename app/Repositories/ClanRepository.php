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



    // =====================================
    // ПОЛУЧЕНИЕ КЛАНА
    // =====================================

    public function find(
        string $tag
    ): ?array
    {
        $stmt =
            $this->db->prepare(
                "
                SELECT *

                FROM clans

                WHERE tag = ?

                LIMIT 1
                "
            );


        $stmt->execute([
            normalizeTag($tag)
        ]);


        $clan =
            $stmt->fetch();


        return $clan ?: null;
    }



    // =====================================
    // СОХРАНЕНИЕ КЛАНА
    // =====================================

    public function save(
        array $clan,
        string $hash
    ): bool
    {
        $tag =
            normalizeTag(
                $clan['tag']
            );


        $exists =
            $this->find(
                $tag
            );



        // Новый клан

        if (!$exists) {

            return $this->create(
                $clan,
                $hash
            );

        }



        // Проверка без изменений

        if (
            $exists['api_hash']
            ===
            $hash
        ) {

            return $this->check(
                $tag
            );

        }



        // Данные изменились

        return $this->update(
            $clan,
            $hash
        );
    }



    // =====================================
    // СОЗДАНИЕ КЛАНА
    // =====================================

    private function create(
        array $clan,
        string $hash
    ): bool
    {
        $data =
            $this->mapClan(
                $clan,
                $hash
            );


        $stmt =
            $this->db->prepare(
                "
                INSERT INTO clans
                (
                    tag,
                    name,
                    description,
                    type,
                    is_family_friendly,

                    location_id,
                    location_name,
                    location_is_country,
                    country_code,

                    language_id,
                    language_name,
                    language_code,

                    required_league_tier_id,
                    required_league_tier_name,

                    clan_level,
                    members,

                    clan_points,
                    clan_builder_base_points,
                    clan_capital_points,

                    war_frequency,
                    war_wins,
                    war_losses,
                    war_ties,
                    war_win_streak,
                    is_war_log_public,

                    war_league_id,
                    war_league_name,

                    capital_league_id,
                    capital_league_name,

                    required_trophies,
                    required_builder_base_trophies,
                    required_townhall_level,

                    capital_hall_level,

                    districts,
                    labels,

                    badge_small,
                    badge_medium,
                    badge_large,

                    api_hash,

                    checks_count,
                    changes_count,
                    recent_checks,

                    created_at,
                    checked_at
                )

                VALUES
                (
                    :tag,
                    :name,
                    :description,
                    :type,
                    :is_family_friendly,

                    :location_id,
                    :location_name,
                    :location_is_country,
                    :country_code,

                    :language_id,
                    :language_name,
                    :language_code,

                    :required_league_tier_id,
                    :required_league_tier_name,

                    :clan_level,
                    :members,

                    :clan_points,
                    :clan_builder_base_points,
                    :clan_capital_points,

                    :war_frequency,
                    :war_wins,
                    :war_losses,
                    :war_ties,
                    :war_win_streak,
                    :is_war_log_public,

                    :war_league_id,
                    :war_league_name,

                    :capital_league_id,
                    :capital_league_name,

                    :required_trophies,
                    :required_builder_base_trophies,
                    :required_townhall_level,

                    :capital_hall_level,

                    :districts,
                    :labels,

                    :badge_small,
                    :badge_medium,
                    :badge_large,

                    :api_hash,

                    1,
                    1,
                    '1',

                    NOW(),
                    NOW()
                )
                "
            );


        return $stmt->execute(
            $data
        );
    }


    // =====================================
    // ОБНОВЛЕНИЕ КЛАНА
    // =====================================

    private function update(
        array $clan,
        string $hash
    ): bool
    {
        $data =
            $this->mapClan(
                $clan,
                $hash
            );



        $stmt =
            $this->db->prepare(
                "
                UPDATE clans

                SET

                    name = :name,
                    description = :description,
                    type = :type,

                    is_family_friendly = :is_family_friendly,

                    location_id = :location_id,
                    location_name = :location_name,
                    location_is_country = :location_is_country,
                    country_code = :country_code,

                    language_id = :language_id,
                    language_name = :language_name,
                    language_code = :language_code,

                    required_league_tier_id = :required_league_tier_id,
                    required_league_tier_name = :required_league_tier_name,

                    clan_level = :clan_level,
                    members = :members,

                    clan_points = :clan_points,
                    clan_builder_base_points = :clan_builder_base_points,
                    clan_capital_points = :clan_capital_points,

                    war_frequency = :war_frequency,
                    war_wins = :war_wins,
                    war_losses = :war_losses,
                    war_ties = :war_ties,
                    war_win_streak = :war_win_streak,
                    is_war_log_public = :is_war_log_public,

                    war_league_id = :war_league_id,
                    war_league_name = :war_league_name,

                    capital_league_id = :capital_league_id,
                    capital_league_name = :capital_league_name,

                    required_trophies = :required_trophies,
                    required_builder_base_trophies = :required_builder_base_trophies,
                    required_townhall_level = :required_townhall_level,

                    capital_hall_level = :capital_hall_level,

                    districts = :districts,
                    labels = :labels,

                    badge_small = :badge_small,
                    badge_medium = :badge_medium,
                    badge_large = :badge_large,


                    api_hash = :api_hash,

                    checks_count = checks_count + 1,
                    changes_count = changes_count + 1,

                    recent_checks =
                        CONCAT(
                            RIGHT(
                                recent_checks,
                                49
                            ),
                            '1'
                        ),

                    updated_at = NOW(),
                    checked_at = NOW()


                WHERE tag = :tag
                "
            );


        return $stmt->execute(
            $data
        );
    }





    // =====================================
    // ПРОВЕРКА БЕЗ ИЗМЕНЕНИЙ
    // =====================================

    private function check(
        string $tag
    ): bool
    {
        $stmt =
            $this->db->prepare(
                "
                UPDATE clans

                SET

                    checks_count =
                        checks_count + 1,


                    recent_checks =
                        CONCAT(
                            RIGHT(
                                recent_checks,
                                49
                            ),
                            '0'
                        ),


                    checked_at = NOW()

                WHERE tag = ?
                "
            );


        return $stmt->execute([
            normalizeTag($tag)
        ]);
    }





    // =====================================
    // ПРЕОБРАЗОВАНИЕ API → DATABASE
    // =====================================

    private function mapClan(
        array $clan,
        string $hash
    ): array
    {
        return [

            'tag' =>
                normalizeTag(
                    $clan['tag']
                ),


            'name' =>
                $clan['name'] ?? '',


            'description' =>
                $clan['description'] ?? null,


            'type' =>
                $clan['type'] ?? '',


            'is_family_friendly' =>
                $clan['isFamilyFriendly']
                ?? 0,



            'location_id' =>
                $clan['location']['id']
                ?? null,


            'location_name' =>
                $clan['location']['name']
                ?? null,


            'location_is_country' =>
                $clan['location']['isCountry']
                ?? null,


            'country_code' =>
                $clan['location']['countryCode']
                ?? null,



            'language_id' =>
                $clan['chatLanguage']['id']
                ?? null,


            'language_name' =>
                $clan['chatLanguage']['name']
                ?? null,


            'language_code' =>
                $clan['chatLanguage']['languageCode']
                ?? null,



            'required_league_tier_id' =>
                $clan['requiredLeagueTier']['id']
                ?? null,


            'required_league_tier_name' =>
                $clan['requiredLeagueTier']['name']
                ?? null,



            'clan_level' =>
                $clan['clanLevel']
                ?? 0,


            'members' =>
                $clan['members']
                ?? 0,


            'clan_points' =>
                $clan['clanPoints']
                ?? 0,


            'clan_builder_base_points' =>
                $clan['clanBuilderBasePoints']
                ?? 0,


            'clan_capital_points' =>
                $clan['clanCapitalPoints']
                ?? 0,



            'war_frequency' =>
                $clan['warFrequency']
                ?? null,


            'war_wins' =>
                $clan['warWins']
                ?? 0,


            'war_losses' =>
                $clan['warLosses']
                ?? 0,


            'war_ties' =>
                $clan['warTies']
                ?? 0,


            'war_win_streak' =>
                $clan['warWinStreak']
                ?? 0,


            'is_war_log_public' =>
                $clan['isWarLogPublic']
                ?? 0,



            'war_league_id' =>
                $clan['warLeague']['id']
                ?? null,


            'war_league_name' =>
                $clan['warLeague']['name']
                ?? null,



            'capital_league_id' =>
                $clan['capitalLeague']['id']
                ?? null,


            'capital_league_name' =>
                $clan['capitalLeague']['name']
                ?? null,



            'required_trophies' =>
                $clan['requiredTrophies']
                ?? 0,


            'required_builder_base_trophies' =>
                $clan['requiredBuilderBaseTrophies']
                ?? 0,


            'required_townhall_level' =>
                $clan['requiredTownhallLevel']
                ?? 0,



            'capital_hall_level' =>
                $clan['clanCapital']['capitalHallLevel']
                ?? 0,


            'districts' =>
                json_encode(
                    $clan['clanCapital']['districts']
                    ?? []
                ),


            'labels' =>
                json_encode(
                    $clan['labels']
                    ?? []
                ),



            'badge_small' =>
                $clan['badgeUrls']['small']
                ?? null,


            'badge_medium' =>
                $clan['badgeUrls']['medium']
                ?? null,


            'badge_large' =>
                $clan['badgeUrls']['large']
                ?? null,


            'api_hash' =>
                $hash

        ];
    }


    // =====================================
    // СПИСОК КЛАНОВ
    // =====================================

    public function all(): array
    {
        $stmt =
            $this->db->query(
                "
                SELECT
                    tag,
                    name

                FROM clans

                ORDER BY name
                "
            );


        return $stmt->fetchAll();
    }





    // =====================================
    // ВСЕ ТЕГИ КЛАНОВ
    // =====================================

    public function getTags(): array
    {
        $stmt =
            $this->db->query(
                "
                SELECT tag

                FROM clans
                "
            );


        return $stmt->fetchAll(
            PDO::FETCH_COLUMN
        );
    }
}