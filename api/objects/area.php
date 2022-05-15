<?php
class Area
{

    // подключение к базе данных и таблице 'area'
    private $conn;
    private $table_name = "area";

    // свойства объекта
    public $id_area;
    public $id_administrator_sites;
    public $title;
    public $id_city;
    public $address;
    public $status;
    public $img_map;


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
                id_administrator_sites = :id_administrator_sites,    
                title = :title,
                id_city = :id_city,
                address = :address,
                status = :status,
                img_map = :img_map";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_administrator_sites = htmlspecialchars(strip_tags($this->id_administrator_sites));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->id_city = htmlspecialchars(strip_tags($this->id_city));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->img_map = htmlspecialchars(strip_tags($this->img_map));

        // привязываем значения
        $stmt->bindParam(':id_administrator_sites', $this->id_administrator_sites);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':id_city', $this->id_city);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam('status', $this->status);
        $stmt->bindParam(':img_map', $this->img_map);

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
