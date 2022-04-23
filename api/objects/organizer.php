<?php
class Organizer
{

    // подключение к базе данных и таблице 'users'
    private $conn;
    private $table_name = "organizer";

    // свойства объекта
    public $id_organizer;
    public $title;
    public $login;
    public $password;
    public $mail;
    public $phone;
    public $payment_card;
    public $id_city;
    public $iat;


    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание нового пользователя
    function create()
    {

        // Вставляем запрос
        $query = "INSERT INTO " . $this->table_name . "
            SET
                title = :title,
                login = :login,
                password = :password,
                mail = :mail,
                phone = :phone,
                payment_card = :payment_card,
                id_city = :id_city";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->mail = htmlspecialchars(strip_tags($this->mail));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->payment_card = htmlspecialchars(strip_tags($this->payment_card));
        $this->id_city = htmlspecialchars(strip_tags($this->id_city));

        // привязываем значения
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':mail', $this->mail);
        $stmt->bindParam('phone', $this->phone);
        $stmt->bindParam(':payment_card', $this->payment_card);
        $stmt->bindParam(':id_city', $this->id_city);

        // для защиты пароля
        // хешируем пароль перед сохранением в базу данных
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);

        // Выполняем запрос
        // Если выполнение успешно, то информация о пользователе будет сохранена в базе данных
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
