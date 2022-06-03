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

// запрашиваем места в зале
$stmt = $place_hall->information_all_place_hall_area();
$num = $stmt->rowCount();

// проверка, найдено ли больше 0 записей
if ($num > 0) {

    // массив мест в зале
    $place_hall_arr = array();
    $place_hall_arr["place_hall_records"] = array();

    // получаем содержимое нашей таблицы
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // извлекаем строку
        extract($row);

        $place_hall_item = array(
            "id_place_hall" => $id_place_hall,
            "id_area" => $id_area,
            "id_types_places" => $id_types_places,
            "row" => $row,
            "place" => $place,
            "status" => $status,
            "x_map" => $x_map,
            "y_map" => $y_map
        );

        array_push($place_hall_arr["place_hall_records"], $place_hall_item);
    }

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode($place_hall_arr);
}

// показать сообщение об отсутствующих местах в зале
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что места в зале не найдены
    echo json_encode(array("message" => "Места в зале не найдены."), JSON_UNESCAPED_UNICODE);
}
