<?php
class City
{

    // подключение к базе данных и таблице 'city'
    private $conn;
    private $table_name = "city";

    // свойства объекта
    public $id_city;
    public $title;

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание нового города
    function create()
    {

        // Вставляем запрос
        $query = "INSERT INTO " . $this->table_name . "
            SET
                title = :title";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->title = htmlspecialchars(strip_tags($this->title));

        // привязываем значения
        $stmt->bindParam(':title', $this->title);

        // Выполняем запрос
        // Если выполнение успешно, то информация о городе будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // получение информации о городе
    function information_city()
    {

        // запрос, чтобы получить данные города
        $query = "SELECT id_city, title
           FROM " . $this->table_name . "
           WHERE id_city = :id_city
           LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_city = htmlspecialchars(strip_tags($this->id_city));

        // привязываем значение id_city
        $stmt->bindParam(':id_city', $this->id_city);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если город есть 
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоим значения свойствам объекта
            $this->id_city = $row['id_city'];
            $this->title = $row['title'];

            // вернём 'true', потому что информация о городе есть
            return true;
        }

        // вернём 'false', если информации о городе нет
        return false;
    }

    // получение информации о всех городах
    function information_all_city()
    {

        // запрос, чтобы получить данные городов
        $query = "SELECT id_city, title
           FROM " . $this->table_name . "
            ";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // выполняем запрос
        $stmt->execute();

        // вернём '$stmt', который содержит информацию о городах
        return $stmt;
    }
}
