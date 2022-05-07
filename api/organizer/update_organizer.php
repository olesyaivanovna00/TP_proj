<?php
// заголовки
header("Access-Control-Allow-Origin: http://SOK/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// требуется для кодирования веб-токена JSON
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

// создание объекта 'Organizer'
$organizer = new Organizer($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// получаем jwt
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

            // Нам нужно установить отправленные данные (через форму HTML) в свойствах объекта организатора
            $organizer->id_organizer = $decoded->data->id_organizer;
            $organizer->title = $data->title;
            $organizer->login = $data->login;
            $organizer->password = $data->password;
            $organizer->mail = $data->mail;
            $organizer->phone = $data->phone;
            $organizer->payment_card = $data->payment_card;
            $organizer->id_city = $data->id_city;

            // устанавливаем значения iat для организатора 
            $organizer->iat = $iat;

            // обновление организатора
            if ($organizer->update() && $organizer->updateIAT()) {
                // нам нужно заново сгенерировать JWT, потому что данные организатора могут отличаться
                $token = array(
                    "iss" => $iss,
                    "sub" => $subO,
                    "aud" => $aud,
                    "iat" => $iat,
                    "data" => array(
                        "id_organizer" => $organizer->id_organizer,
                        "login" => $organizer->login,
                        "mail" => $organizer->mail
                    )
                );

                $jwt = JWT::encode($token, $key, 'HS256');

                // код ответа
                http_response_code(200);

                // ответ в формате JSON
                echo json_encode(
                    array(
                        "message" => "Организатор был обновлён",
                        "jwt" => $jwt
                    )
                );
            }

            // сообщение, если не удается обновить организатора
            else {
                // код ответа
                http_response_code(401);

                // показать сообщение об ошибке
                echo json_encode(array("message" => "Невозможно обновить организатора."));
            }
        }

        // сообщение, если iat для организатора устарел
        else {
            // код ответа
            http_response_code(401);

            // показать сообщение об ошибке
            echo json_encode(array("message" => "Токен устарел, невозможно обновить организатора."));
        }
    }

    // если декодирование не удалось, это означает, что JWT является недействительным
    catch (Exception $e) {

        // код ответа
        http_response_code(401);

        // сообщение об ошибке
        echo json_encode(array(
            "message" => "Доступ закрыт",
            "error" => $e->getMessage()
        ));
    }
}

// показать сообщение об ошибке, если jwt пуст
else {

    // код ответа
    http_response_code(401);

    // сообщить организатору что доступ запрещен
    echo json_encode(array("message" => "Доступ закрыт."));
}
