<?php
class Ticket
{

    // подключение к базе данных и таблице 'ticket'
    private $conn;
    private $table_name = "ticket";

    // свойства объекта
    public $id_ticket;
    public $id_concert;
    public $id_place_hall;
    public $status;
    public $id_users;
    public $mail;
    public $price;

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Создание нового билета
    function create()
    {

        // Вставляем запрос
        $query = "INSERT INTO " . $this->table_name . "
        SET  
            id_concert = :id_concert,
            id_place_hall = :id_place_hall,
            status = :status,
            price = :price";
        // id_users = :id_users,
        // mail = :mail,
        
        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->id_concert = htmlspecialchars(strip_tags($this->id_concert));
        $this->id_place_hall = htmlspecialchars(strip_tags($this->id_place_hall));
        $this->status = htmlspecialchars(strip_tags($this->status));
        //$this->id_users = htmlspecialchars(strip_tags($this->id_users));
        //$this->mail = htmlspecialchars(strip_tags($this->mail));
        $this->price = htmlspecialchars(strip_tags($this->price));

        // привязываем значения
        $stmt->bindParam(':id_concert', $this->id_concert);
        $stmt->bindParam(':id_place_hall', $this->id_place_hall);
        $stmt->bindParam(':status', $this->status);
        //$stmt->bindParam(':id_users', $this->id_users);
        //$stmt->bindParam(':mail', $this->mail);
        $stmt->bindParam(':price', $this->price);

        // Выполняем запрос
        // Если выполнение успешно, то информация о новом билете будет сохранена в базе данных
        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
