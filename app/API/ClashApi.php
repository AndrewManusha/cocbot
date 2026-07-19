<?php


class ClashApi
{

    private string $baseUrl =
        'https://api.clashofclans.com/v1/';



    /**
     * Выполняет GET-запрос к Clash of Clans API
     *
     * @param string $endpoint
     * @return array|false
     */
    private function request($endpoint)
    {

        $url =
            $this->baseUrl .
            ltrim($endpoint, '/');


        $ch =
            curl_init($url);


        curl_setopt_array($ch, [

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_CONNECTTIMEOUT => 10,

            CURLOPT_TIMEOUT => 20,

            CURLOPT_HTTPHEADER => [

                'Authorization: Bearer ' . CLASH_API_TOKEN,

                'Accept: application/json'

            ]

        ]);


        $response =
            curl_exec($ch);


        if (curl_errno($ch)) {

            curl_close($ch);

            return false;

        }


        $httpCode =
            curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );


        curl_close($ch);



        if ($httpCode !== 200) {

            return false;

        }



        $data =
            json_decode(
                $response,
                true
            );



        if (!is_array($data)) {

            return false;

        }


        return $data;

    }





    /**
     * Получить информацию о клане
     *
     * @param string $tag
     * @return array|false
     */
    public function getClan($tag)
    {

        $tag =
            normalizeTag(
                $tag
            );


        return $this->request(
            "clans/%23{$tag}"
        );

    }





    /**
     * Получить информацию об игроке
     *
     * @param string $tag
     * @return array|false
     */
    public function getPlayer($tag)
    {

        $tag =
            normalizeTag(
                $tag
            );


        return $this->request(
            "players/%23{$tag}"
        );

    }





    /**
     * Получить текущую войну клана
     *
     * @param string $tag
     * @return array|false
     */
    public function getCurrentWar($tag)
    {

        $tag =
            normalizeTag(
                $tag
            );


        return $this->request(
            "clans/%23{$tag}/currentwar"
        );

    }





    /**
     * Получить участников клана
     *
     * @param string $tag
     * @return array|false
     */
    public function getClanMembers($tag)
    {

        $tag =
            normalizeTag(
                $tag
            );


        return $this->request(
            "clans/%23{$tag}/members"
        );

    }





    /**
     * Получить текущую КВЛ
     *
     * @param string $tag
     * @return array|false
     */
    public function getCurrentWarLeagueGroup($tag)
    {

        $tag =
            normalizeTag(
                $tag
            );


        return $this->request(
            "clans/%23{$tag}/currentwar/leaguegroup"
        );

    }

}