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

// запрашиваем исполнителей
$stmt = $executor->information_all_executor();
$num = $stmt->rowCount();

// проверка, найдено ли больше 0 записей
if ($num > 0) {

    // массив исполнителей
    $executor_arr = array();
    $executor_arr["executor_records"] = array();

    // получаем содержимое нашей таблицы
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // извлекаем строку
        extract($row);

        $executor_item = array(
            "id_executor" => $id_executor,
            "title" => $title
        );

        array_push($executor_arr["executor_records"], $executor_item);
    }

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode($executor_arr);
}

// показать сообщение об отсутствующих исполнителях
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что исполнители не найдены
    echo json_encode(array("message" => "Исполнители не найдены."), JSON_UNESCAPED_UNICODE);
}
