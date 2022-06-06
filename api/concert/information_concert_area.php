<?php
// заголовки
header("Access-Control-Allow-Origin: http://SOK/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// подключение к БД
// файлы, необходимые для подключения к базе данных
include_once '../config/database.php';
include_once '../objects/concert.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Concert'
$concert = new Concert($db);

// получаем значение JSON
$data = json_decode(file_get_contents("php://input"));

// получаем id_area
$id_area = isset($data->id_area) ? $data->id_area : "";
$concert->id_area = $id_area;

// запрашиваем концерты
$stmt = $concert->information_all_concert_area();
$num = $stmt->rowCount();

// проверка, найдено ли больше 0 записей
if ($num > 0) {

    // массив концертов
    $concert_arr = array();
    $concert_arr["concert_records"] = array();

    // получаем содержимое нашей таблицы
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // извлекаем строку
        extract($row);

        $concert_item = array(
            "id_concert" => $id_concert,
            "id_organizer" => $id_organizer,
            "date_concert" => $date_concert,
            "time_start_sale" => $time_start_sale,
            "time_end_sale" => $time_end_sale,
            "age_restriction" => $age_restriction,
            "id_genre" => $id_genre,
            "id_area" => $id_area,
            "broadcast" => $broadcast,
            "img_promo" => $img_promo,
            "description_promo" => $description_promo
        );

        array_push($concert_arr["concert_records"], $concert_item);
    }

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode($concert_arr);
}

// показать сообщение об отсутствующих конертах
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что концерты не найдены
    echo json_encode(array("message" => "Концерты не найдены."), JSON_UNESCAPED_UNICODE);
}
