<?php

class TelegramService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = API_URL;
    }

    /**
     * Универсальный запрос к Telegram API
     */
    public function request(string $method, array $data = []): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . $method,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($curl);

        if ($response === false) {

            writeLog(
                "Telegram CURL ERROR: " .
                curl_error($curl)
            );

            curl_close($curl);

            return [
                'ok' => false
            ];
        }

        curl_close($curl);

        $decoded = json_decode($response, true);

        if (!is_array($decoded)) {

            writeLog(
                "Telegram JSON ERROR: " .
                $response
            );

            return [
                'ok' => false
            ];
        }

        return $decoded;
    }

    /**
     * Отправить сообщение
     */
    public function sendMessage(
        int|string $chatId,
        ?int $threadId,
        string $text,
        array $options = []
    ): array
    {
        $data = [

            'chat_id' => $chatId,

            'text' => $text,

            'parse_mode' =>
                DEFAULT_PARSE_MODE,

            'disable_web_page_preview' =>
                true

        ];

        if ($threadId !== null) {

            $data['message_thread_id'] =
                $threadId;

        }

        $data = array_merge(
            $data,
            $options
        );

        return $this->request(
            'sendMessage',
            $data
        );
    }

    /**
     * Изменить сообщение
     */
    public function editMessage(
        int|string $chatId,
        int $messageId,
        string $text,
        array $options = []
    ): array
    {
        $data = [

            'chat_id' => $chatId,

            'message_id' => $messageId,

            'text' => $text,

            'parse_mode' =>
                DEFAULT_PARSE_MODE,

            'disable_web_page_preview' =>
                true

        ];

        $data = array_merge(
            $data,
            $options
        );

        return $this->request(
            'editMessageText',
            $data
        );
    }

    /**
     * Удалить сообщение
     */
    public function deleteMessage(
        int|string $chatId,
        int $messageId
    ): array
    {
        return $this->request(
            'deleteMessage',
            [

                'chat_id' => $chatId,

                'message_id' => $messageId

            ]
        );
    }

    /**
     * Ответить на Callback
     */
    public function answerCallback(
        string $callbackId,
        string $text = ''
    ): array
    {
        $data = [

            'callback_query_id' =>
                $callbackId

        ];

        if ($text !== '') {

            $data['text'] = $text;

        }

        return $this->request(
            'answerCallbackQuery',
            $data
        );
    }
}