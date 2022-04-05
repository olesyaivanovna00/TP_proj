<?php
class Users
{

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
                name = :name,
                mail = :mail,
                phone = :phone,
                password = :password,
                payment_card = :payment_card";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->mail = htmlspecialchars(strip_tags($this->mail));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->payment_card = htmlspecialchars(strip_tags($this->payment_card));

        // привязываем значения
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':mail', $this->mail);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':payment_card', $this->payment_card);

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

    // проверка есть ли электронная почта в базе
    function emailExists()
    {

        // запрос, чтобы проверить, существует ли электронная почта
        $query = "SELECT id_users, name, mail, phone, password, payment_card
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
            $this->id_users = $row['id_users'];
            $this->name = $row['name'];
            $this->mail = $row['mail'];
            $this->password = $row['password'];

            // вернём 'true', потому что в базе данных существует электронная почта
            return true;
        }

        // вернём 'false', если адрес электронной почты не существует в базе данных
        return false;
    }

    // обновить запись пользователя
    public function update()
    {

        // Если в HTML-форме был введен пароль (необходимо обновить пароль)
        $password_set = !empty($this->password) ? ", password = :password" : "";

        // если не введен пароль - не обновлять пароль
        $query = "UPDATE " . $this->table_name . "
            SET
                name = :name,
                mail = :mail,
                phone = :phone,
                payment_card = :payment_card
                {$password_set}
            WHERE id_users = :id_users";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция (очистка)
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->mail = htmlspecialchars(strip_tags($this->mail));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->payment_card = htmlspecialchars(strip_tags($this->payment_card));

        // привязываем значения с HTML формы
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':mail', $this->mail);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':payment_card', $this->payment_card);
      
        // метод password_hash () для защиты пароля пользователя в базе данных
        if (!empty($this->password)) {
            $this->password = htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_users', $this->id_users);

        // Если выполнение успешно, то информация о пользователе будет сохранена в базе данных
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
