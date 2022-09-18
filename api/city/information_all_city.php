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

// запрашиваем города
$stmt = $city->information_all_city();
$num = $stmt->rowCount();

// проверка, найдено ли больше 0 записей
if ($num > 0) {

    // массив городов
    $city_arr = array();
    $city_arr["city_records"] = array();

    // получаем содержимое нашей таблицы
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // извлекаем строку
        extract($row);

        $city_item = array(
            "id_city" => $id_city,
            "title" => $title
        );

        array_push($city_arr["city_records"], $city_item);
    }

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode($city_arr);
}

// показать сообщение об отсутствующих городах
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что города не найдены
    echo json_encode(array("message" => "Города не найдены."), JSON_UNESCAPED_UNICODE);
}
