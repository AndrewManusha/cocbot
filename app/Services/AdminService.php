<?php


class AdminService
{


    private AdminRepository $admins;



    public function __construct()
    {

        $this->admins =
            adminRepository();

    }





    public function isAdmin(int $telegram_id): bool
    {

        return $this->admins
            ->isAdmin(
                $telegram_id
            );

    }

}