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

// создание объекта 'concert'
$concert = new Concert($db);

// получаем значение JSON
$data = json_decode(file_get_contents("php://input"));

// получаем id_concert
$id_concert = isset($data->id_concert) ? $data->id_concert : "";
$concert->id_concert = $id_concert;

//есть ли в базе информация о концерте
$information_data = $concert->information_concert();

if ($information_data) {

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode(
        array(
            "id_concert" => $concert->id_concert,
            "id_organizer" => $concert->id_organizer,
            "date_concert" => $concert->date_concert,
            "time_start_sale" => $concert->time_start_sale,
            "time_end_sale" => $concert->time_end_sale,
            "age_restriction" => $concert->age_restriction,
            "id_genre" => $concert->id_genre,
            "id_area" => $concert->id_area,
            "broadcast" => $concert->broadcast,
            "img_promo" => $concert->img_promo,
            "description_promo" => $concert->description_promo
        )
    );
}

// показать сообщение об отсутствующем концерте
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что концерт не найден
    echo json_encode(array("message" => "Концерт не найден."), JSON_UNESCAPED_UNICODE);
}
