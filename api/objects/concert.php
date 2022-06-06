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
        $stmt->bindParam(':id_organizer', $this->id_organizer);
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

    // обновить концерт
    public function update_concert()
    {

        // Вставляем запрос
        $query = "UPDATE " . $this->table_name . "
            SET
                date_concert = :date_concert,
                time_start_sale = :time_start_sale,
                time_end_sale = :time_end_sale,
                age_restriction = :age_restriction,
                id_genre = :id_genre
            WHERE id_concert = :id_concert";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->date_concert = htmlspecialchars(strip_tags($this->date_concert));
        $this->time_start_sale = htmlspecialchars(strip_tags($this->time_start_sale));
        $this->time_end_sale = htmlspecialchars(strip_tags($this->time_end_sale));
        $this->age_restriction = htmlspecialchars(strip_tags($this->age_restriction));
        $this->id_genre = htmlspecialchars(strip_tags($this->id_genre));

        // привязываем значения
        $stmt->bindParam(':date_concert', $this->date_concert);
        $stmt->bindParam(':time_start_sale', $this->time_start_sale);
        $stmt->bindParam(':time_end_sale', $this->time_end_sale);
        $stmt->bindParam(':age_restriction', $this->age_restriction);
        $stmt->bindParam(':id_genre', $this->id_genre);

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_concert', $this->id_concert);

        // Если выполнение успешно, то информация о концерте будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // обновить промо концерта
    public function update_promo()
    {

        // Вставляем запрос
        $query = "UPDATE " . $this->table_name . "
            SET
                img_promo = :img_promo,
                description_promo = :description_promo
            WHERE id_concert = :id_concert";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->img_promo = htmlspecialchars(strip_tags($this->img_promo));
        $this->description_promo = htmlspecialchars(strip_tags($this->description_promo));

        // привязываем значения
        $stmt->bindParam(':img_promo', $this->img_promo);
        $stmt->bindParam(':description_promo', $this->description_promo);

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_concert', $this->id_concert);

        // Если выполнение успешно, то информация о промо концерта будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // обновить трансляцию концерта
    public function update_broadcast()
    {

        // Вставляем запрос
        $query = "UPDATE " . $this->table_name . "
            SET
                broadcast = :broadcast                
            WHERE id_concert = :id_concert";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->broadcast = htmlspecialchars(strip_tags($this->broadcast));

        // привязываем значения
        $stmt->bindParam(':broadcast', $this->broadcast);

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id_concert', $this->id_concert);

        // Если выполнение успешно, то информация о трансляции концерта будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // получение информации о концерте
    function information_concert()
    {

        // запрос, чтобы получить данные о концерте
        $query = "SELECT id_concert, id_organizer, date_concert, time_start_sale, time_end_sale, age_restriction, id_genre, id_area, broadcast, img_promo, description_promo
           FROM " . $this->table_name . "
           WHERE id_concert = :id_concert
           LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_concert = htmlspecialchars(strip_tags($this->id_concert));

        // привязываем значение id_concert
        $stmt->bindParam(':id_concert', $this->id_concert);

        // выполняем запрос
        $stmt->execute();

        // получаем количество строк
        $num = $stmt->rowCount();

        // если концерт есть 
        if ($num > 0) {

            // получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоим значения свойствам объекта
            $this->id_concert = $row['id_concert'];
            $this->id_organizer = $row['id_organizer'];
            $this->date_concert = $row['date_concert'];
            $this->time_start_sale = $row['time_start_sale'];
            $this->time_end_sale = $row['time_end_sale'];
            $this->age_restriction = $row['age_restriction'];
            $this->id_genre = $row['id_genre'];
            $this->id_area = $row['id_area'];
            $this->broadcast = $row['broadcast'];
            $this->img_promo = $row['img_promo'];
            $this->description_promo = $row['description_promo'];

            // вернём 'true', потому что информация о концерте есть
            return true;
        }

        // вернём 'false', если информации о концерте нет
        return false;
    }

    // получение информации о всех концертах на площадке
    function information_all_concert_area()
    {

        // запрос, чтобы получить данные городов
        $query = "SELECT id_concert, id_organizer, date_concert, time_start_sale, time_end_sale, age_restriction, id_genre, id_area, broadcast, img_promo, description_promo
        FROM " . $this->table_name . " 
        WHERE id_area = :id_area 
        ORDER BY date_concert ASC";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_concert = htmlspecialchars(strip_tags($this->id_concert));

        // привязываем значение id_concert
        $stmt->bindParam(':id_area', $this->id_concert);

        // выполняем запрос
        $stmt->execute();

        // вернём '$stmt', который содержит информацию о городах
        return $stmt;
    }
}
