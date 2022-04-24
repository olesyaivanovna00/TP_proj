<?php
class Organizer
{

    // подключение к базе данных и таблице 'organizer'
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

    // Создание нового организатора
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

    // проверка есть ли электронная почта в базе
    function emailExists()
    {

        // запрос, чтобы проверить, существует ли электронная почта
        $query = "SELECT id_organizer, title, login, password, mail, phone, payment_card, id_city, iat
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
            $this->id_organizer = $row['id_organizer'];
            $this->title = $row['title'];
            $this->login = $row['login'];
            $this->password = $row['password'];
            $this->mail = $row['mail'];
            $this->phone = $row['phone'];
            $this->payment_card = $row['payment_card'];
            $this->id_city = $row['id_city'];
            $this->iat = $row['iat'];

            // вернём 'true', потому что в базе данных существует электронная почта
            return true;
        }

        // вернём 'false', если адрес электронной почты не существует в базе данных
        return false;
    }

    // обновить запись организатора
    public function update()
    {

        // Если в HTML-форме был введен пароль (необходимо обновить пароль)
        $password_set = !empty($this->password) ? ", password = :password" : "";

        // если не введен пароль - не обновлять пароль
        $query = "UPDATE " . $this->table_name . "
            SET
                title = :title,
                login = :login,
                mail = :mail,
                phone = :phone,
                payment_card = :payment_card,
                id_city = :id_city
                {$password_set}
            WHERE id_organizer = :id_organizer";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция (очистка)
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->mail = htmlspecialchars(strip_tags($this->mail));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->payment_card = htmlspecialchars(strip_tags($this->payment_card));
        $this->id_city = htmlspecialchars(strip_tags($this->id_city));


        // привязываем значения с HTML формы
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':mail', $this->mail);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':payment_card', $this->payment_card);
        $stmt->bindParam(':id_city', $this->id_city);

        // метод password_hash () для защиты пароля организатора в базе данных
        if (!empty($this->password)) {
            $this->password = htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_organizer', $this->id_organizer);

        // Если выполнение успешно, то информация об организаторе будет сохранена в базе данных
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // обновить IAT организатора
    public function updateIAT()
    {

        $query = "UPDATE " . $this->table_name . "
            SET
                iat = :iat                
            WHERE id_organizer = :id_organizer";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция (очистка)
        $this->iat = htmlspecialchars(strip_tags($this->iat));

        // привязываем значения с HTML формы
        $stmt->bindParam(':iat', $this->iat);

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_organizer', $this->id_organizer);

        // Если выполнение успешно, то информация о IAT организатора будет сохранена в базе данных
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // проверить IAT организатора
    public function checkIAT()
    {

        // запрос, чтобы проверить, верно ли IAT организатора
        $query = "SELECT id_organizer, iat
               FROM " . $this->table_name . "
               WHERE id_organizer = :id_organizer
               LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_organizer = htmlspecialchars(strip_tags($this->id_organizer));

        // привязываем значение id_organizer
        $stmt->bindParam(':id_organizer', $this->id_organizer);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если IAT организатора существует,
        // то проверим является ли действительным токен
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($this->iat == $row['iat']) {

                // вернём 'true', потому что в базе данных IAT организатора соответствует токену
                return true;
            }
        }

        // вернём 'false', если IAT организатора не соответствует токену 
        return false;
    }

    // проверить SUB организатора
    public function checkSUB()
    {
        // если SUB организатора существует,
        // то проверим является ли он правильным
        if ($this->sub == $this->subCheck) {

            // вернём 'true', потому что SUB организатора соответствует токену
            return true;
        }

        // вернём 'false', если SUB организатора не соответствует токену 
        return false;
    }

    // получение информации о организаторе для обновления данных о нем
    function information_organizer()
    {

        // запрос, чтобы получить данные организатора
        $query = "SELECT id_organizer, title, login, mail, phone, payment_card, id_city
           FROM " . $this->table_name . "
           WHERE id_organizer = :id_organizer
           LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_organizer = htmlspecialchars(strip_tags($this->id_organizer));

        // привязываем значение id_organizer
        $stmt->bindParam(':id_organizer', $this->id_organizer);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если организатор есть 
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоим значения свойствам объекта
            $this->id_organizer = $row['id_organizer'];
            $this->title = $row['title'];
            $this->login = $row['login'];
            $this->mail = $row['mail'];
            $this->phone = $row['phone'];
            $this->payment_card = $row['payment_card'];
            $this->id_city = $row['id_city'];

            // вернём 'true', потому что информация о организаторе есть
            return true;
        }

        // вернём 'false', если информации о организаторе нет
        return false;
    }
}
