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
include_once '../objects/administrator_sites.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Administrator_sites'
$administrator_sites = new Administrator_sites($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// устанавливаем значения
$administrator_sites->login = $data->login;
$administrator_sites->password = $data->password;
$administrator_sites->mail = $data->mail;
$administrator_sites->phone = $data->phone;
$administrator_sites->id_city = $data->id_city;

// создание пользователя
if (
    !empty($administrator_sites->login) &&
    !empty($administrator_sites->password) &&
    !empty($administrator_sites->mail) &&
    (!empty($administrator_sites->phone) || $administrator_sites->phone == "") &&
    !empty($administrator_sites->id_city)
) {

    // если все параметры переданы, создаем администратора площадок
    if ($administrator_sites->create()) {

        // устанавливаем код ответа
        http_response_code(200);

        // покажем сообщение о том, что администратор площадок был создан
        echo json_encode(array("message" => "Администратор площадок был создан."));
    }

    // сообщение, если не удаётся создать администратора площадок
    else {

        // устанавливаем код ответа
        http_response_code(400);

        // покажем сообщение о том, что создать администратора площадок не удалось
        echo json_encode(array("message" => "Невозможно создать администратора площадок."));
    }
}

// сообщение, если не удаётся создать администратора площадок
else {

    // устанавливаем код ответа
    http_response_code(400);

    // покажем сообщение о том, что создать администратора площадок не удалось
    echo json_encode(array("message" => "Невозможно создать администратора площадок, не хватает параметров"));
}
