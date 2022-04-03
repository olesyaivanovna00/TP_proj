<?php
class Users {

    // подключение к базе данных и таблице 'users'
    private $conn;
    private $table_name = "users";

    // свойства объекта
    public $id_users;
    public $name;
    public $mail;
    public $phone;
    public $password;
    public $payment_card;
    

    // конструктор для соединения с базой данных
    public function __construct($db){
        $this->conn = $db;
    }

    // здесь будет метод read()
}
?>
