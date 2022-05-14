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

    // обновить запись администратора площадок
    public function update()
    {

        // Если в HTML-форме был введен пароль (необходимо обновить пароль)
        $password_set = !empty($this->password) ? ", password = :password" : "";

        // если не введен пароль - не обновлять пароль
        $query = "UPDATE " . $this->table_name . "
            SET
                login = :login,
                mail = :mail,
                phone = :phone,
                id_city = :id_city
                {$password_set}
            WHERE id_administrator_sites = :id_administrator_sites";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция (очистка)
        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->mail = htmlspecialchars(strip_tags($this->mail));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->id_city = htmlspecialchars(strip_tags($this->id_city));


        // привязываем значения с HTML формы
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':mail', $this->mail);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':id_city', $this->id_city);

        // метод password_hash () для защиты пароля администратора площадок в базе данных
        if (!empty($this->password)) {
            $this->password = htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_administrator_sites', $this->id_administrator_sites);

        // Если выполнение успешно, то информация об администраторе площадок будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // обновить IAT администратора площадок
    public function updateIAT()
    {

        $query = "UPDATE " . $this->table_name . "
            SET
                iat = :iat                
            WHERE id_administrator_sites = :id_administrator_sites";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция (очистка)
        $this->iat = htmlspecialchars(strip_tags($this->iat));

        // привязываем значения с сервера
        $stmt->bindParam(':iat', $this->iat);

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_administrator_sites', $this->id_administrator_sites);

        // Если выполнение успешно, то информация о IAT администратора площадок будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // проверить IAT администратора площадок
    public function checkIAT()
    {

        // запрос, чтобы проверить, верно ли IAT администратора площадок
        $query = "SELECT id_administrator_sites, iat
               FROM " . $this->table_name . "
               WHERE id_administrator_sites = :id_administrator_sites
               LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_administrator_sites = htmlspecialchars(strip_tags($this->id_administrator_sites));

        // привязываем значение id_administrator_sites
        $stmt->bindParam(':id_administrator_sites', $this->id_administrator_sites);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если IAT администратора площадок существует,
        // то проверим является ли действительным токен
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($this->iat == $row['iat']) {

                // вернём 'true', потому что в базе данных IAT администратора площадок соответствует токену
                return true;
            }
        }

        // вернём 'false', если IAT администратора площадок не соответствует токену 
        return false;
    }

    // проверить SUB администратора площадок
    public function checkSUB()
    {
        // если SUB администратора площадок существует,
        // то проверим является ли он правильным
        if ($this->sub == $this->subCheck) {

            // вернём 'true', потому что SUB администратора площадок соответствует токену
            return true;
        }

        // вернём 'false', если SUB администратора площадок не соответствует токену 
        return false;
    }

    // получение информации об администраторе площадок для обновления данных о нем
    function information_administrator_sites()
    {

        // запрос, чтобы получить данные администратора площадок
        $query = "SELECT id_administrator_sites, login, mail, phone, id_city
           FROM " . $this->table_name . "
           WHERE id_administrator_sites = :id_administrator_sites
           LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_administrator_sites = htmlspecialchars(strip_tags($this->id_administrator_sites));

        // привязываем значение id_administrator_sites
        $stmt->bindParam(':id_administrator_sites', $this->id_administrator_sites);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если администратор площадок есть 
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоим значения свойствам объекта
            $this->id_administrator_sites = $row['id_administrator_sites'];
            $this->login = $row['login'];
            $this->mail = $row['mail'];
            $this->phone = $row['phone'];
            $this->id_city = $row['id_city'];

            // вернём 'true', потому что информация об администраторе площадок есть
            return true;
        }

        // вернём 'false', если информации об администраторе площадок нет
        return false;
    }
}
