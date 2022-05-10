<?php
class Genre
{

    // подключение к базе данных и таблице 'genre'
    private $conn;
    private $table_name = "genre";

    // свойства объекта
    public $id_genre;
    public $title;

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание нового жанра
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
        // Если выполнение успешно, то информация о жанре будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // получение информации о жанре
    function information_genre()
    {

        // запрос, чтобы получить данные жанра
        $query = "SELECT id_genre, title
           FROM " . $this->table_name . "
           WHERE id_genre = :id_genre
           LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_genre = htmlspecialchars(strip_tags($this->id_genre));

        // привязываем значение id_genre
        $stmt->bindParam(':id_genre', $this->id_genre);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если жанр есть 
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоим значения свойствам объекта
            $this->id_genre = $row['id_genre'];
            $this->title = $row['title'];

            // вернём 'true', потому что информация о жанре есть
            return true;
        }

        // вернём 'false', если информации о жанре нет
        return false;
    }

    // получение информации о всех жанрах
    function information_all_genre()
    {

        // запрос, чтобы получить данные жанров
        $query = "SELECT id_genre, title
           FROM " . $this->table_name . "
            ";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // выполняем запрос
        $stmt->execute();

        // вернём '$stmt', который содержит информацию о жанрах
        return $stmt;
    }
}
