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
include_once '../objects/types_places.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Types_places'
$types_places = new Types_places($db);

// получаем значение JSON
$data = json_decode(file_get_contents("php://input"));

// получаем id_types_places
$id_types_places = isset($data->id_types_places) ? $data->id_types_places : "";
$types_places->id_types_places = $id_types_places;

//есть ли в базе информация о типе мест
$information_data = $types_places->information_types_places();

if ($information_data) {

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode(
        array(
            "id_types_places" => $types_places->id_types_places,
            "title" => $types_places->title,
            "description" => $types_places->description,
            "units" => $types_places->units,
            "status" => $types_places->status,
            "id_area" => $types_places->id_area
        )
    );
}

// показать сообщение об отсутствующем типе мест
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что тип мест не найден
    echo json_encode(array("message" => "Типа мест не найден."), JSON_UNESCAPED_UNICODE);
}
