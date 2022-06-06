<?php
class Concert_participants
{

    // подключение к базе данных и таблице 'concert_participants'
    private $conn;
    private $table_name = "concert_participants";

    // свойства объекта
    public $id_concert;
    public $id_executor;


    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание участников концерта
    function create()
    {

        // Вставляем запрос
        $query = "INSERT INTO " . $this->table_name . "
        SET  
            id_concert = :id_concert,
            id_executor = :id_executor";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_concert = htmlspecialchars(strip_tags($this->id_concert));
        $this->id_executor = htmlspecialchars(strip_tags($this->id_executor));

        // привязываем значения
        $stmt->bindParam(':id_concert', $this->id_concert);
        $stmt->bindParam(':id_executor', $this->id_executor);

        // Выполняем запрос
        // Если выполнение успешно, то информация о новом участнике концерта будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
