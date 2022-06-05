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
include_once '../objects/place_hall.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Place_hall'
$place_hall = new Place_hall($db);

// получаем значение JSON
$data = json_decode(file_get_contents("php://input"));

// получаем id_place_hall
$id_place_hall = isset($data->id_place_hall) ? $data->id_place_hall : "";
$place_hall->id_place_hall = $id_place_hall;

//есть ли в базе информация о месте в зале
$information_data = $place_hall->information_place_hall();

if ($information_data) {

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode(
        array(
            "id_place_hall" => $id_place_hall,
            "id_area" => $id_area,
            "id_types_places" => $id_types_places,
            "row" => $row,
            "place" => $place,
            "status" => $status,
            "x_map" => $x_map,
            "y_map" => $y_map
        )
    );
}

// показать сообщение об отсутствующем месте в зале
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что тип мест не найден
    echo json_encode(array("message" => "Место в зале не найдено."), JSON_UNESCAPED_UNICODE);
}
