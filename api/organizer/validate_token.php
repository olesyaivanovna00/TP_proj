<?php
// заголовки
header("Access-Control-Allow-Origin: http://SOK/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// требуется для декодирования JWT
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
include_once '../libs/php-jwt-master/src/Key.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

// файлы, необходимые для подключения к базе данных
include_once '../config/database.php';
include_once '../objects/organizer.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'User'
$organizer = new Organizer($db);

// получаем значение веб-токена JSON
$data = json_decode(file_get_contents("php://input"));

// получаем JWT
$jwt = isset($data->jwt) ? $data->jwt : "";

// если JWT не пуст
if ($jwt) {

    // если декодирование выполнено успешно, показать данные организатора
    try {
        // декодирование jwt
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        // устанавливаем значения iat для организатора 
        $organizer->id_organizer = $decoded->data->id_organizer;
        $organizer->iat = $decoded->iat;

        // устанавливаем значения sub для организатора 
        $organizer->sub = $decoded->sub;
        $organizer->subCheck = $subO;

        //проверяем значения
        if ($organizer->checkIAT() && $organizer->checkSUB()) {

            // код ответа
            http_response_code(200);

            // показать детали
            echo json_encode(array(
                "message" => "Доступ разрешен.",
                "data" => $decoded->data

            ));
        }
        // показать сообщение об устаревшем токене
        else {

            // код ответа
            http_response_code(401);

            // сообщить организатору что доступ запрещен
            echo json_encode(array("message" => "Токен устарел, доступ запрещён."));
        }
    }

    // если декодирование не удалось, это означает, что JWT является недействительным
    catch (Exception $e) {

        // код ответа
        http_response_code(401);

        // сообщить организатору отказано в доступе и показать сообщение об ошибке
        echo json_encode(array(
            "message" => "Доступ закрыт.",
            "error" => $e->getMessage()

        ));
    }
}

// показать сообщение об ошибке, если jwt пуст
else {

    // код ответа
    http_response_code(401);

    // сообщить организатору что доступ запрещен
    echo json_encode(array("message" => "Доступ запрещён."));
}
