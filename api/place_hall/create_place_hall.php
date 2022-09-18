<?php
// требуемые заголовки
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

// подключение к БД
// файлы, необходимые для подключения к базе данных
include_once '../config/database.php';
include_once '../objects/place_hall.php';
include_once '../objects/administrator_sites.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Place_hall'
$place_hall = new Place_hall($db);

// создание объекта 'Administrator_sites'
$administrator_sites = new Administrator_sites($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// получаем jwt
$jwt = isset($data->jwt) ? $data->jwt : "";

// если JWT не пуст
if ($jwt) {

    // если декодирование выполнено успешно, показать данные места
    try {

        // декодирование jwt
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        // устанавливаем значения iat для администратора площадок
        $administrator_sites->id_administrator_sites = $decoded->data->id_administrator_sites;
        $administrator_sites->iat = $decoded->iat;

        // устанавливаем значения sub для администратора площадок
        $administrator_sites->sub = $decoded->sub;
        $administrator_sites->subCheck = $subA;

        //проверяем значения
        if ($administrator_sites->checkIAT() && $administrator_sites->checkSUB()) {

            // Нам нужно установить отправленные данные (через форму HTML) в свойствах объекта место в зале
            $place_hall->id_area = $data->id_area;
            $place_hall->id_types_places = $data->id_types_places;
            $place_hall->row = $data->row;
            $place_hall->place = $data->place;
            $place_hall->status = $data->status;
            $place_hall->x_map = $data->x_map;
            $place_hall->y_map = $data->y_map;

            // создание места в зале
            if (
                !empty($place_hall->id_area) &&
                !empty($place_hall->id_types_places) &&
                (!empty($place_hall->row) || $place_hall->row == "") &&
                !empty($place_hall->place) &&
                !empty($place_hall->status) &&
                (!empty($place_hall->x_map) || $place_hall->x_map == "") &&
                (!empty($place_hall->y_map) || $place_hall->y_map == "")

            ) {

                // если все параметры переданы, создаем место в зале
                if ($place_hall->create()) {

                    // устанавливаем код ответа
                    http_response_code(200);

                    // покажем сообщение о том, что место в зале создано
                    echo json_encode(array("message" => "Мест в зале создано."));
                }

                // сообщение, если не удаётся создать место в зале
                else {

                    // устанавливаем код ответа
                    http_response_code(400);

                    // покажем сообщение о том, что создать место в зале не удалось
                    echo json_encode(array("message" => "Невозможно создать место в зале."));
                }
            }

            // сообщение, если не удаётся создать место в зале
            else {

                // устанавливаем код ответа
                http_response_code(400);

                // покажем сообщение о том, что создать место в зале не удалось
                echo json_encode(array("message" => "Невозможно создать место в зале, не хватает параметров"));
            }
        }

        // сообщение, если iat для администратора площадок устарел
        else {
            // код ответа
            http_response_code(401);

            // показать сообщение об ошибке
            echo json_encode(array("message" => "Токен устарел, невозможно создать место в зале."));
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

    // сообщить администратору площадок что доступ запрещен
    echo json_encode(array("message" => "Доступ закрыт."));
}
