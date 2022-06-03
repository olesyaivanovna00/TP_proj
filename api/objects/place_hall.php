<?php
class Place_hall
{

    // подключение к базе данных и таблице 'place_hall'
    private $conn;
    private $table_name = "place_hall";

    // свойства объекта
    public $id_place_hall;
    public $id_area;
    public $id_types_places;
    public $row;
    public $place;
    public $status;
    public $x_map;
    public $y_map;


    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание нового места
    function create()
    {

        // Вставляем запрос
        $query = "INSERT INTO " . $this->table_name . "
            SET  
                id_area = :id_area,
                id_types_places = :id_types_places,
                row = :row,
                place = :place,
                status = :status,
                x_map = :x_map,
                y_map = :y_map";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_area = htmlspecialchars(strip_tags($this->id_area));
        $this->id_types_places = htmlspecialchars(strip_tags($this->id_types_places));
        $this->row = htmlspecialchars(strip_tags($this->row));
        $this->place = htmlspecialchars(strip_tags($this->place));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->x_map = htmlspecialchars(strip_tags($this->x_map));
        $this->y_map = htmlspecialchars(strip_tags($this->y_map));

        // привязываем значения
        $stmt->bindParam(':id_area', $this->id_area);
        $stmt->bindParam(':id_types_places', $this->id_types_places);
        $stmt->bindParam(':row', $this->row);
        $stmt->bindParam(':place', $this->place);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':x_map', $this->x_map);
        $stmt->bindParam(':y_map', $this->y_map);

        // Выполняем запрос
        // Если выполнение успешно, то информация о новом мест будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // обновить место в зале
    public function update()
    {

        // Вставляем запрос
        $query = "UPDATE " . $this->table_name . "
            SET
                row = :row,
                place = :place,
                status = :status,
                x_map = :x_map,
                y_map = :y_map
            WHERE id_place_hall = :id_place_hall";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->row = htmlspecialchars(strip_tags($this->row));
        $this->place = htmlspecialchars(strip_tags($this->place));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->x_map = htmlspecialchars(strip_tags($this->x_map));
        $this->y_map = htmlspecialchars(strip_tags($this->y_map));

        // привязываем значения
        $stmt->bindParam(':row', $this->row);
        $stmt->bindParam(':place', $this->place);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':x_map', $this->x_map);
        $stmt->bindParam(':y_map', $this->y_map);

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_place_hall', $this->id_place_hall);

        // Если выполнение успешно, то информация о месте в зале будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
