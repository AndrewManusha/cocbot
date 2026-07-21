<?php


class ClashApi
{

    private string $baseUrl =
        'https://api.clashofclans.com/v1/';



    // =====================================
    // ВЫПОЛНЕНИЕ API ЗАПРОСА
    // =====================================

    private function request(
        string $endpoint
    ): array|false
    {
        $url =
            $this->baseUrl .
            ltrim(
                $endpoint,
                '/'
            );


        $ch =
            curl_init(
                $url
            );


        curl_setopt_array(
            $ch,
            [
                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_CONNECTTIMEOUT => 10,

                CURLOPT_TIMEOUT => 20,

                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' .
                    CLASH_API_TOKEN,

                    'Accept: application/json'
                ]
            ]
        );


        $response =
            curl_exec(
                $ch
            );


        if (
            curl_errno($ch)
        ) {

            curl_close($ch);

            return false;
        }


        $status =
            curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );


        curl_close($ch);


        if (
            $status !== 200
        ) {

            return false;
        }


        $data =
            json_decode(
                $response,
                true
            );


        return is_array($data)
            ? $data
            : false;
    }



    // =====================================
    // ПОЛУЧИТЬ КЛАН
    // =====================================

    public function getClan(
        string $tag
    ): array|false
    {
        $tag =
            normalizeTag(
                $tag
            );


        if (
            $tag === ''
        ) {

            return false;
        }


        return $this->request(
            "clans/%23{$tag}"
        );
    }



    // =====================================
    // ПОЛУЧИТЬ ИГРОКА
    // =====================================

    public function getPlayer(
        string $tag
    ): array|false
    {
        $tag =
            normalizeTag(
                $tag
            );


        if (
            $tag === ''
        ) {

            return false;
        }


        return $this->request(
            "players/%23{$tag}"
        );
    }



    // =====================================
    // ТЕКУЩАЯ ВОЙНА
    // =====================================

    public function getCurrentWar(
        string $tag
    ): array|false
    {
        $tag =
            normalizeTag(
                $tag
            );


        if (
            $tag === ''
        ) {

            return false;
        }


        return $this->request(
            "clans/%23{$tag}/currentwar"
        );
    }



    // =====================================
    // ГРУППА КВЛ
    // =====================================

    public function getCurrentWarLeagueGroup(
        string $tag
    ): array|false
    {
        $tag =
            normalizeTag(
                $tag
            );


        if (
            $tag === ''
        ) {

            return false;
        }


        return $this->request(
            "clans/%23{$tag}/currentwar/leaguegroup"
        );
    }

}