<?php

namespace App\app;

use PDO;

require_once 'config/database.php';

class Users
{
    private $db_name = DB_NAME;
    private $table_users = TABLE_USERS;
    private $table_phones = TABLE_PHONES;
    private $pdo;

    public function __construct()
    {
        require_once 'DbConnect.php';
        $this->pdo = DbConnect::getInstance();
        $this->pdo->getPdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    /**
     * Console command "php app/Users.php getUser id"
     * @param $id
     */
    public function getUser($id)
    {
        if (!empty($id)) {
            $this->pdo->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt = $this->pdo->getPdo()->prepare(
                "SELECT users.name, users.birthday, phones.number FROM $this->db_name.$this->table_users AS users
                JOIN $this->db_name.$this->table_phones AS phones ON users.id = phones.user_id WHERE users.id = :id"
            );
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $getCountPhone = $this->pdo->getPdo()->prepare("SELECT * FROM $this->db_name.$this->table_phones WHERE user_id=?");
            $getCountPhone->execute(array($id));

            $index = 1;
            $count = $getCountPhone->rowCount();
            foreach ($stmt->fetchAll() as $value) {
                $user['name'] = $value['name'];
                $user['birthday'] = $value['birthday'];
                if ($index == $count) {
                    $user['phone'] .= $value['number'];
                } else {
                    $user['phone'] .= $value['number'] . ', ';
                }
                $index++;
            }
            var_dump($user);
        }
    }

    /**
     * Console command "php app/Users.php earnedPhone phone sum"
     * @param $number
     * @param $sum
     * @return false|void
     */
    public function earnedPhone($number, $sum)
    {
        if ($sum <= 100 && $this->checkNumber($number)) {
            $stmt = $this->pdo->getPdo()->prepare("SELECT id FROM $this->db_name.$this->table_phones WHERE number LIKE ?");;
            $stmt->execute(array('%' . $number . '%'));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $save = $this->pdo->getPdo()->prepare(
                    "UPDATE $this->db_name.$this->table_phones SET balance=?, update_at=? 
                            WHERE id=?"
                );
                $save->execute(array($sum, date('Y-m-d H:i:s', strtotime("now")), $user['id']));
            } else {
                echo "Пользователь не найден";
                return false;
            }
        } else {
            echo "Сумма или телефон введены не корректно";
            return false;
        }
    }

    /**
     * Console command php app/Users.php addUser 'name' 'birthday' 'phone' 'phone'
     * You can specify several numbers
     * @param $name
     * @param $birthday
     * @param ...$phone
     * @return false|void
     */
    public function addUser($name, $birthday, ...$phone)
    {
//        check the user name is not empty, birthday have a format dd-mm-YYYY and phone number is correct format
        if (!empty($name) && preg_match('/^\d{2}-\d{2}-\d{4}$/', $birthday) && preg_grep('/^380(50|67|63|68)\d{7}$/', $phone[0])) {
            $userPhones = preg_grep('/^380(50|67|63|68)\d{7}$/', $phone[0]);
            $this->pdo->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt = $this->pdo->getPdo()->prepare(
                "INSERT INTO $this->db_name.$this->table_users (name, birthday) VALUES (:name, :birthday)"
            );
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':birthday', $birthday);
            $stmt->execute();

            $lastId = $this->pdo->getPdo()->lastInsertId();

            $stmt2 = $this->pdo->getPdo()->prepare(
                "INSERT INTO $this->db_name.$this->table_phones (user_id, number, code, balance) 
                VALUES (:user_id, :number, :code, :balance)"
            );
            $stmt2->bindParam(':user_id', $lastId);
            $stmt2->bindParam(':number', $number);
            $stmt2->bindParam(':code', $code);
            $stmt2->bindParam(':balance', $balance);

            $arrCode = [];
            foreach ($userPhones as $phone) {
                $number = $phone;
                preg_match_all('/^\d{3}(50|67|63|68)/', $phone, $arrCode, PREG_SET_ORDER);
                $code = $arrCode[0][1];
                $balance = 0;
                $stmt2->execute();
            }
        } else {
            echo "Данные введены не корректно";
            return false;
        }

    }

    /**
     * Console command php app/Users.php addPhoneForUser 'id' 'phone'
     * @param $user_id
     * @param $number
     * @return false|void
     */
    public function addPhoneForUser($user_id, $number)
    {
        if (!empty($user_id) && $this->checkNumber($number)) {
            $this->pdo->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//            find user
            $stmt = $this->pdo->getPdo()->prepare(
                "SELECT id FROM $this->db_name.$this->table_users WHERE id = :id"
            );
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $user = $stmt->fetchAll();

//            check if phone not exist in table
            $getPhone = $this->pdo->getPdo()->prepare(
                "SELECT * FROM $this->db_name.$this->table_phones WHERE number = :number"
            );
            $getPhone->bindParam(':number', $number);
            $getPhone->execute();
            $getPhone->setFetchMode(PDO::FETCH_ASSOC);
            $phone = $getPhone->fetch();

//            if user exist and phone not exist in table
            if ($user && !$phone) {
                $stmt2 = $this->pdo->getPdo()->prepare(
                    "INSERT INTO $this->db_name.$this->table_phones (user_id, number, code, balance)
                VALUES (:user_id, :number, :code, :balance)"
                );
                $stmt2->bindParam(':user_id', $user_id);
                $stmt2->bindParam(':number', $number);
                $stmt2->bindParam(':code', $code);
                $stmt2->bindParam(':balance', $balance);

                $getCode = [];
                preg_match_all('/^\d{3}(50|67|63|68)/', $number, $getCode, PREG_SET_ORDER);
                $code = $getCode[0][1];
                $balance = 0;
                $stmt2->execute();
            } else {
                echo "Пользователь не найден или мобильный телефон имеется в базе";
                return false;
            }
        } else {
            echo "Данне введены не верны";
            return false;
        }
    }

    /**
     * Console command php app/Users.php delete 'id'
     * @param $id
     * @return false|void
     */
    public function delete($id)
    {
        if (!empty($id)) {
            $save = $this->pdo->getPdo()->prepare("UPDATE $this->db_name.$this->table_users SET delete_at=? WHERE id=?");;
            $save->execute(array(date('Y-m-d H:i:s', strtotime("now")), $id));
        } else {
            echo "Пользователь не найден";
            return false;
        }
    }

    private function checkNumber($phone)
    {
        return preg_match('/^380(50|67|63|68)\d{7}$/', $phone);
    }
}


$user = new Users();
//  get methods name and params
switch ($argv[1]) {
    case 'getUser':
        $user->getUser($argv[2]);
        break;
    case 'earnedPhone':
        $user->earnedPhone($argv[2], $argv[3]);
        break;
    case 'addUser':
        $user->addUser($argv[2], $argv[3], $argv);
        break;
    case 'addPhoneForUser':
        $user->addPhoneForUser($argv[2], $argv[3]);
        break;
    case 'delete':
        $user->delete($argv[2]);
        break;
}
