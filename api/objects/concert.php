<?php
class Concert
{

    // подключение к базе данных и таблице 'concert'
    private $conn;
    private $table_name = "concert";

    // свойства объекта
    public $id_concert;
    public $id_organizer;
    public $date_concert;
    public $time_start_sale;
    public $time_end_sale;
    public $age_restriction;
    public $id_genre;
    public $id_area;
    public $broadcast;
    public $img_promo;
    public $description_promo;


    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Создание нового концерта
    function create()
    {

        // Вставляем запрос
        $query = "INSERT INTO " . $this->table_name . "
        SET  
            id_organizer = :id_organizer,
            date_concert = :date_concert,
            time_start_sale = :time_start_sale,
            time_end_sale = :time_end_sale,
            age_restriction = :age_restriction,
            id_genre = :id_genre,
            id_area = :id_area";
        /*  broadcast = :broadcast,
            img_promo = :img_promo,
            description_promo = :description_promo";*/

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_organizer = htmlspecialchars(strip_tags($this->id_organizer));
        $this->date_concert = htmlspecialchars(strip_tags($this->date_concert));
        $this->time_start_sale = htmlspecialchars(strip_tags($this->time_start_sale));
        $this->time_end_sale = htmlspecialchars(strip_tags($this->time_end_sale));
        $this->age_restriction = htmlspecialchars(strip_tags($this->age_restriction));
        $this->id_genre = htmlspecialchars(strip_tags($this->id_genre));
        $this->id_area = htmlspecialchars(strip_tags($this->id_area));

        // привязываем значения
        $stmt->bindParam(':id_area', $this->id_area);
        $stmt->bindParam(':date_concert', $this->date_concert);
        $stmt->bindParam(':time_start_sale', $this->time_start_sale);
        $stmt->bindParam(':time_end_sale', $this->time_end_sale);
        $stmt->bindParam(':age_restriction', $this->age_restriction);
        $stmt->bindParam(':id_genre', $this->id_genre);
        $stmt->bindParam(':id_area', $this->id_area);

        // Выполняем запрос
        // Если выполнение успешно, то информация о новом концерте будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
