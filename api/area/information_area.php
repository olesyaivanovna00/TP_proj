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
include_once '../objects/area.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Area'
$area = new Area($db);

// получаем значение JSON
$data = json_decode(file_get_contents("php://input"));

// получаем id_area
$id_area = isset($data->id_area) ? $data->id_area : "";
$area->id_area = $id_area;

//есть ли в базе информация о площадке
$information_data = $area->information_area();

if ($information_data) {

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode(
        array(
            "id_area" => $area->id_area,
            "id_administrator_sites" => $area->id_administrator_sites,
            "title" => $area->title,
            "id_city" => $area->id_city,
            "address" => $area->address,
            "status" => $area->status,
            "img_map" => $area->img_map
        )
    );
}

// показать сообщение об отсутствующей площадке
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что площадка не найдена
    echo json_encode(array("message" => "Площадка не найдена."), JSON_UNESCAPED_UNICODE);
}
