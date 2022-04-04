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
include_once '../objects/users.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Users'
$users = new Users($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// устанавливаем значения
$users->name = $data->name;
$users->mail = $data->mail;
$users->phone = $data->phone;
$users->password = $data->password;
$users->payment_card = $data->payment_card;

// создание пользователя
if (
    !empty($users->name) &&
    !empty($users->mail) &&
    (!empty($users->phone) || $users->phone == "") &&
    !empty($users->password) &&
    (!empty($users->payment_card) || $users->payment_card == "") &&
    $users->create()
) {
    // устанавливаем код ответа
    http_response_code(200);

    // покажем сообщение о том, что пользователь был создан
    echo json_encode(array("message" => "Пользователь был создан."));
}

// сообщение, если не удаётся создать пользователя
else {

    // устанавливаем код ответа
    http_response_code(400);

    // покажем сообщение о том, что создать пользователя не удалось
    echo json_encode(array("message" => "Невозможно создать пользователя."));
}
