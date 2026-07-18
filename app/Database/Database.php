<?php


class Database
{


    private PDO $connection;



    public function __construct()
    {

        try {

            $this->connection =
                new PDO(

                    "mysql:host=" . DB_HOST .
                    ";dbname=" . DB_NAME .
                    ";charset=" . DB_CHARSET,

                    DB_USER,

                    DB_PASS

                );


            $this->connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );


            $this->connection->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );


        }
        catch(PDOException $e)
        {


            file_put_contents(

                LOG_FILE,

                "[" . date("d.m.Y H:i:s") . "] DATABASE ERROR: " .
                $e->getMessage() .
                PHP_EOL,

                FILE_APPEND

            );


            exit;

        }

    }




    public function getConnection(): PDO
    {

        return $this->connection;

    }


}