<?php

namespace App\app;

require_once 'config/database.php';

use \PDO;

class DbConnect
{
    private $pdo;

    private static $instances;

    private function __construct()
    {
        $this->pdo = new PDO(DSN, NAME, PASSWORD);
    }

    public static function getInstance()
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
