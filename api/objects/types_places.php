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
    public $status;
    public $id_area;


    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание нового типа мест
    function create()
    {

        // Вставляем запрос
        $query = "INSERT INTO " . $this->table_name . "
            SET  
                title = :title,
                description = :description,
                units = :units,
                status = :status,
                id_area = :id_area";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->units = htmlspecialchars(strip_tags($this->units));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id_area = htmlspecialchars(strip_tags($this->id_area));

        // привязываем значения
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':units', $this->units);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id_area', $this->id_area);

        // Выполняем запрос
        // Если выполнение успешно, то информация о типе мест будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // получение информации о типе мест
    function information_types_places()
    {

        // запрос, чтобы получить данные о типе мест
        $query = "SELECT id_types_places, title, description, units, status, id_area
           FROM " . $this->table_name . "
           WHERE id_types_places = :id_types_places
           LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_types_places = htmlspecialchars(strip_tags($this->id_types_places));

        // привязываем значение id_types_places
        $stmt->bindParam(':id_types_places', $this->id_types_places);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если тип мест есть 
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоим значения свойствам объекта
            $this->id_types_places = $row['id_types_places'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->units = $row['units'];
            $this->status = $row['status'];
            $this->id_area = $row['id_area'];

            // вернём 'true', потому что информация о типе мест есть
            return true;
        }

        // вернём 'false', если информации о типе мест нет
        return false;
    }
}
