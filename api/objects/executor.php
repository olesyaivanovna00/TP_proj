<?php
class Executor
{

    // подключение к базе данных и таблице 'executor'
    private $conn;
    private $table_name = "executor";

    // свойства объекта
    public $id_executor;
    public $title;

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание нового исполнителя
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
        // Если выполнение успешно, то информация о исполнителе будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // получение информации о исполнителе
    function information_executor()
    {

        // запрос, чтобы получить данные исполнителя
        $query = "SELECT id_executor, title
           FROM " . $this->table_name . "
           WHERE id_executor = :id_executor
           LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_executor = htmlspecialchars(strip_tags($this->id_executor));

        // привязываем значение id_executor
        $stmt->bindParam(':id_executor', $this->id_executor);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если исполнитель есть 
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоим значения свойствам объекта
            $this->id_executor = $row['id_executor'];
            $this->title = $row['title'];

            // вернём 'true', потому что информация о исполнителе есть
            return true;
        }

        // вернём 'false', если информации о исполнителе нет
        return false;
    }

    // получение информации о всех исполнителях
    function information_all_executor()
    {

        // запрос, чтобы получить данные исполнителей
        $query = "SELECT id_executor, title
           FROM " . $this->table_name . "
            ";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // выполняем запрос
        $stmt->execute();

        // вернём '$stmt', который содержит информацию о исполнителях
        return $stmt;
    }
}
