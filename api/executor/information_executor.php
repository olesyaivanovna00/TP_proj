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
include_once '../objects/executor.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Executor'
$executor = new Executor($db);

// получаем значение JSON
$data = json_decode(file_get_contents("php://input"));

// получаем id_executor
$id_executor = isset($data->id_executor) ? $data->id_executor : "";
$executor->id_executor = $id_executor;

//есть ли в базе информация о исполнителе
$information_data = $executor->information_executor();

if ($information_data) {

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode(
        array(
            "id_executor" => $executor->id_executor,
            "title" => $executor->title
        )
    );
}

// показать сообщение об отсутствующем исполнителе
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что исполнитель не найден
    echo json_encode(array("message" => "Исполнитель не найден."), JSON_UNESCAPED_UNICODE);
}
