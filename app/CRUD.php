<?php

namespace App\app;

require_once 'config/database.php';

use App\seeds\DataBaseSeeder;
use Exception;

class CRUD
{
    private $obj;
    private $db_name = DB_NAME;
    private $table_users = TABLE_USERS;
    private $table_phones = TABLE_PHONES;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        require_once 'DbConnect.php';
        $this->obj = DbConnect::getInstance();
        $this->create();
    }

    /**
     * @throws Exception
     */
    public function create()
    {
        if (!$this->tableExists()) {
            echo 'Create DB' . PHP_EOL;
            //Creation of user "user_name"
            $this->obj->getPdo()->query("CREATE USER 'user_name'@'%' IDENTIFIED BY 'pass_word';");
            //Creation of database "new_db"
            $this->obj->getPdo()->query("CREATE DATABASE $this->db_name;");
            //Adding all privileges on our newly created database
            $this->obj->getPdo()->query("GRANT ALL PRIVILEGES on $this->db_name.* TO 'user_name'@'%';");
            // create table
            $this->obj->getPdo()->query("CREATE TABLE $this->db_name.$this->table_users (
   id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(30) DEFAULT NULL,
   birthday VARCHAR(10) DEFAULT NULL,
   create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   delete_at TIMESTAMP DEFAULT NULL                   
   )");
            $this->obj->getPdo()->query("CREATE TABLE $this->db_name.$this->table_phones (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) DEFAULT NULL,
    number VARCHAR(12) DEFAULT NULL,
    code INT(2) DEFAULT NULL,
    balance FLOAT DEFAULT NULL,
    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
            $this->addDataToDB();
        } else {
            echo 'DB if exist' . PHP_EOL;
        }
    }

    /**
     * Check DB is creat or not. Return true if DB exist
     * @return bool
     */
    private function tableExists(): bool
    {
        $searchSchema = gettype($this->obj->getPdo()->exec("SELECT count(*) FROM $this->db_name.$this->table_users")) == 'integer';
        return (bool)$searchSchema;
    }

    /**
     * @throws Exception
     */
    public function addDataToDB()
    {
        include_once 'seeds/DataBaseSeeder.php';
        DataBaseSeeder::createData();
    }
}
