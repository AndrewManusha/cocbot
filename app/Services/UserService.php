<?php


class UserService
{

    private UserRepository $users;



    public function __construct()
    {
        $this->users =
            userRepository();
    }





    // =====================================
    // РЕГИСТРАЦИЯ / ОБНОВЛЕНИЕ АКТИВНОСТИ
    // =====================================

    public function register(array $user): void
    {

        $exists =
            $this->users->find(
                $user['id']
            );



        if (!$exists) {


            $this->users->create([

                'telegram_id' =>
                    $user['id'],

                'username' =>
                    $user['username']
                    ??
                    '',

                'first_name' =>
                    $user['first_name']
                    ??
                    '',

                'last_name' =>
                    $user['last_name']
                    ??
                    ''

            ]);


            writeLog(
                "Добавлен пользователь " .
                (
                    $user['username']
                    ??
                    $user['id']
                )
            );


        }
        else {


            $this->users
                ->updateActivity(
                    $user['id']
                );

        }

    }





    // =====================================
    // ПОЛУЧИТЬ ВСЕХ
    // =====================================

    public function all(): array
    {
        return $this->users->all();
    }





    // =====================================
    // АДМИНЫ
    // =====================================

    public function admins(): array
    {
        return $this->users->allAdmins();
    }





    // =====================================
    // БЕЗ АДМИНОВ
    // =====================================

    public function members(): array
    {
        return $this->users->allWithoutAdmins();
    }

}