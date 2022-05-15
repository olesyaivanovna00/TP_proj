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
include_once '../objects/city.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'City'
$city = new City($db);

// получаем значение JSON
$data = json_decode(file_get_contents("php://input"));

// получаем id_city
$id_city = isset($data->id_city) ? $data->id_city : "";
$city->id_city = $id_city;

//есть ли в базе информация о городе
$information_data = $city->information_city();

if ($information_data) {

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode(
        array(
            "id_city" => $city->id_city,
            "title" => $city->title
        )
    );
}

// показать сообщение об отсутствующем городе
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что город не найден
    echo json_encode(array("message" => "Город не найден."), JSON_UNESCAPED_UNICODE);
}
