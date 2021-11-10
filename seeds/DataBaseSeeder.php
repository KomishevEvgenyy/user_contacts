<?php

namespace App\seeds;

require_once 'config/database.php';

use App\app\DbConnect;
use Exception;
use PDO;

class DataBaseSeeder
{
    const COUNTRY_CODE = 380;
    const OPERATOR_CODE = [50, 67, 63, 68];
    const USER_FIRST_NAME = [
        ' Александр', 'Дмитрий', 'Максим', 'Даниил', 'Кирилл', 'Ярослав', 'Денис', 'Никита', 'Иван', ' Артём',
        'Тимофей', 'Богдан', 'Глеб', 'Захар', 'Матвей'
    ];
    const MIN_BALANCE = -50;
    const MAX_BALANCE = 150;

    /**
     * @return bool
     * @throws Exception
     */
    public static function createData(): bool
    {
        $db_name = DB_NAME;
        $table_users = TABLE_USERS;
        $table_phones = TABLE_PHONES;
        try {
            $pdo = DbConnect::getInstance();
            $pdo->getPdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->getPdo()->prepare(
                "INSERT INTO $db_name.$table_users (name, birthday) VALUES (:name, :birthday)"
            );
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':birthday', $birthday);

            for ($i = 0; $i < 2000; $i++) {
//                Records for the table Users
                $name = self::USER_FIRST_NAME[array_rand(self::USER_FIRST_NAME, 1)];
                $birthday = date('d-m-Y', mt_rand(315602761, 1005247561));
                $stmt->execute();
                $lastId = $pdo->getPdo()->lastInsertId();

                $stmt2 = $pdo->getPdo()->prepare(
                    "INSERT INTO $db_name.$table_phones (user_id, number, code, balance) VALUES ($lastId,:number, :code, :balance)"
                );
                $stmt2->bindParam(':number', $phone);
                $stmt2->bindParam(':code', $code);
                $stmt2->bindParam(':balance', $balance);

                for ($j = 0; $j < mt_rand(1, 3); $j++) {
//                    Records for the table Phones
                    $code = self::OPERATOR_CODE[array_rand(self::OPERATOR_CODE, 1)];
                    $phone = self::COUNTRY_CODE . $code . rand(1000000.00, 9999999.99);
                    $balance = rand(self::MIN_BALANCE, self::MAX_BALANCE) . '.' . rand(01, 99);
                    $stmt2->execute();
                }

            }
            echo "Table created" . PHP_EOL;
        } catch (\PDOException $exception) {
            var_dump($exception);
        }
        return true;
    }
}