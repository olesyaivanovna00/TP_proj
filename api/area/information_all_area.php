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

// запрашиваем площадки
$stmt = $area->information_all_area();
$num = $stmt->rowCount();

// проверка, найдено ли больше 0 записей
if ($num > 0) {

    // массив площадок
    $area_arr = array();
    $area_arr["area_records"] = array();

    // получаем содержимое нашей таблицы
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // извлекаем строку
        extract($row);

        $area_item = array(
            "id_area" => $id_area,
            "id_administrator_sites" => $id_administrator_sites,
            "title" => $title,
            "id_city" => $id_city,
            "address" => $address,
            "status" => $status,
            "img_map" => $img_map
        );

        array_push($area_arr["area_records"], $area_item);
    }

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode($area_arr);
}
// показать сообщение об отсутствующих площадках
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что площадки не найдены
    echo json_encode(array("message" => "Площадки не найдены."), JSON_UNESCAPED_UNICODE);
}
