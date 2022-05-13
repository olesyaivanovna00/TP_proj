<?php
class Administrator_sites
{

    // подключение к базе данных и таблице 'administrator_sites'
    private $conn;
    private $table_name = "administrator_sites";

    // свойства объекта
    public $id_administrator_sites;
    public $login;
    public $password;
    public $mail;
    public $phone;
    public $id_city;
    public $iat;


    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание нового администратора площадок
    function create()
    {

        // Вставляем запрос
        $query = "INSERT INTO " . $this->table_name . "
            SET
                login = :login,
                password = :password,
                mail = :mail,
                phone = :phone,
                id_city = :id_city";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->mail = htmlspecialchars(strip_tags($this->mail));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->id_city = htmlspecialchars(strip_tags($this->id_city));

        // привязываем значения
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':mail', $this->mail);
        $stmt->bindParam('phone', $this->phone);
        $stmt->bindParam(':id_city', $this->id_city);

        // для защиты пароля
        // хешируем пароль перед сохранением в базу данных
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);

        // Выполняем запрос
        // Если выполнение успешно, то информация об администраторе площадок будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // проверка есть ли электронная почта в базе
    function emailExists()
    {

        // запрос, чтобы проверить, существует ли электронная почта
        $query = "SELECT id_administrator_sites, login, password, mail, phone, id_city, iat
            FROM " . $this->table_name . "
            WHERE mail = :mail
            LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->mail = htmlspecialchars(strip_tags($this->mail));

        // привязываем значение mail
        $stmt->bindParam(':mail', $this->mail);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если электронная почта существует,
        // присвоим значения свойствам объекта для легкого доступа и использования для php сессий
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоим значения свойствам объекта
            $this->id_administrator_sites = $row['id_administrator_sites'];
            $this->login = $row['login'];
            $this->password = $row['password'];
            $this->mail = $row['mail'];
            $this->phone = $row['phone'];
            $this->id_city = $row['id_city'];
            $this->iat = $row['iat'];

            // вернём 'true', потому что в базе данных существует электронная почта
            return true;
        }

        // вернём 'false', если адрес электронной почты не существует в базе данных
        return false;
    }
}
