<?php
class Types_places
{

    // подключение к базе данных и таблице 'types_places'
    private $conn;
    private $table_name = "types_places";

    // свойства объекта
    public $id_types_places;
    public $title;
    public $description;
    public $units;
    public $id_area;


    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание новой площадки
    function create()
    {

        // Вставляем запрос
        $query = "INSERT INTO " . $this->table_name . "
            SET  
                title = :title,
                description = :description,
                units = :units,
                id_area = :id_area";
       
        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->units = htmlspecialchars(strip_tags($this->units));
        $this->id_area = htmlspecialchars(strip_tags($this->id_area));
       
        // привязываем значения
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':units', $this->units);
        $stmt->bindParam(':id_area', $this->id_area);
        
        // Выполняем запрос
        // Если выполнение успешно, то информация о площадке будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
