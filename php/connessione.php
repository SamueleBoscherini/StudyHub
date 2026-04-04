<?php

class Database
{
    private static ?Database $instance = null;
    private \mysqli $connessione;

    private function __construct()
    {
        $host = "my_mariadb";
        $user = "root";
        $pass = "progetto_pasqua";
        $db = "StudyHub";
        $this->connessione = new mysqli($host, $user, $pass, $db);
        if ($connessione->connect_error) {
            die("Connessione fallita: " . $connessione->connect_error);
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \mysqli
    {
        return $this->connessione;
    }
}