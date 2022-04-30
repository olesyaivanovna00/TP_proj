<?php
// требуемые заголовки
header("Access-Control-Allow-Origin: http://SOK/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// подключение к БД
// файлы, необходимые для подключения к базе данных
include_once '../config/database.php';
include_once '../objects/organizer.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Organizer'
$organizer = new Organizer($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// устанавливаем значения
$organizer->title = $data->title;
$organizer->login = $data->login;
$organizer->password = $data->password;
$organizer->mail = $data->mail;
$organizer->phone = $data->phone;
$organizer->payment_card = $data->payment_card;
$organizer->id_city = $data->id_city;

// создание пользователя
if (
    !empty($organizer->title) &&
    !empty($organizer->login) &&
    !empty($organizer->password) &&
    !empty($organizer->mail) &&
    (!empty($organizer->phone) || $organizer->phone == "") &&
    (!empty($organizer->payment_card) || $organizer->payment_card == "") &&
    !empty($organizer->id_city)
) {

    // если все параметры переданы, создаем организатора
    if ($organizer->create()) {

        // устанавливаем код ответа
        http_response_code(200);

        // покажем сообщение о том, что организатор был создан
        echo json_encode(array("message" => "Организатор был создан."));
    }

    // сообщение, если не удаётся создать организатора
    else {

        // устанавливаем код ответа
        http_response_code(400);

        // покажем сообщение о том, что создать организатора не удалось
        echo json_encode(array("message" => "Невозможно создать организатора."));
    }
}

// сообщение, если не удаётся создать организатора
else {

    // устанавливаем код ответа
    http_response_code(400);

    // покажем сообщение о том, что создать организатора не удалось
    echo json_encode(array("message" => "Невозможно создать организатора, не хватает параметров"));
}
