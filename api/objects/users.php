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
        $query = "SELECT id_users, name, mail, phone, password, payment_card, iat
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
            $this->iat = $row['iat'];

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
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // обновить IAT пользователя
    public function updateIAT()
    {

        $query = "UPDATE " . $this->table_name . "
            SET
                iat = :iat                
            WHERE id_users = :id_users";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция (очистка)
        $this->iat = htmlspecialchars(strip_tags($this->iat));

        // привязываем значения с HTML формы
        $stmt->bindParam(':iat', $this->iat);

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_users', $this->id_users);

        // Если выполнение успешно, то информация о IAT пользователя будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // проверить IAT пользователя
    public function checkIAT()
    {

        // запрос, чтобы проверить, верно ли IAT пользователя
        $query = "SELECT id_users, iat
            FROM " . $this->table_name . "
            WHERE id_users = :id_users
            LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_users = htmlspecialchars(strip_tags($this->id_users));

        // привязываем значение id_users
        $stmt->bindParam(':id_users', $this->id_users);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если IAT пользователя существует,
        // то проверим является ли действительным токен
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($this->iat == $row['iat']) {

                // вернём 'true', потому что в базе данных IAT пользователя соответствует токену
                return true;
            }
        }

        // вернём 'false', если IAT пользователя не соответствует токену 
        return false;
    }

    // проверить SUB пользователя
    public function checkSUB()
    {
        // если SUB пользователя существует,
        // то проверим является ли он правильным
        if ($this->sub == $this->subCheck) {

            // вернём 'true', потому что SUB пользователя соответствует токену
            return true;
        }

        // вернём 'false', если SUB пользователя не соответствует токену 
        return false;
    }

    // получение информации о пользователе для обновления данных о нем
    function information_users()
    {

        // запрос, чтобы получить данные пользователя
        $query = "SELECT id_users, name, mail, phone, payment_card
           FROM " . $this->table_name . "
           WHERE id_users = :id_users
           LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_users = htmlspecialchars(strip_tags($this->id_users));

        // привязываем значение id_users
        $stmt->bindParam(':id_users', $this->id_users);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если пользователь есть 
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоим значения свойствам объекта
            $this->id_users = $row['id_users'];
            $this->name = $row['name'];
            $this->mail = $row['mail'];
            $this->phone = $row['phone'];
            $this->payment_card = $row['payment_card'];

            // вернём 'true', потому что информация о пользователе есть
            return true;
        }

        // вернём 'false', если информации о пользователе нет
        return false;
    }
}
