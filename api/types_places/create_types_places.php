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
include_once '../objects/types_places.php';
include_once '../objects/administrator_sites.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Types_places'
$types_places = new Types_places($db);

// создание объекта 'Administrator_sites'
$administrator_sites = new Administrator_sites($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// получаем jwt
$jwt = isset($data->jwt) ? $data->jwt : "";

// если JWT не пуст
if ($jwt) {

    // если декодирование выполнено успешно, показать данные типа мест
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

            // Нам нужно установить отправленные данные (через форму HTML) в свойствах объекта тип мест
            $types_places->title = $data->title;
            $types_places->description = $data->description;
            $types_places->units = $data->units;
            $types_places->status = $data->status;
            $types_places->id_area = $data->id_area;

            // создание типа мест
            if (
                !empty($types_places->title) &&
                (!empty($types_places->description) || $types_places->description == "") &&
                !empty($types_places->units) &&
                !empty($types_places->status) &&
                !empty($types_places->id_area)
            ) {

                // если все параметры переданы, создаем тип мест
                if ($types_places->create()) {

                    // устанавливаем код ответа
                    http_response_code(200);

                    // покажем сообщение о том, что тип мест создан
                    echo json_encode(array("message" => "Тип мест создан."));
                }

                // сообщение, если не удаётся создать тип мест
                else {

                    // устанавливаем код ответа
                    http_response_code(400);

                    // покажем сообщение о том, что создать тип мест не удалось
                    echo json_encode(array("message" => "Невозможно создать тип мест."));
                }
            }

            // сообщение, если не удаётся создать тип мест
            else {

                // устанавливаем код ответа
                http_response_code(400);

                // покажем сообщение о том, что создать тип мест не удалось
                echo json_encode(array("message" => "Невозможно создать тип мест, не хватает параметров"));
            }
        }

        // сообщение, если iat для администратора площадок устарел
        else {
            // код ответа
            http_response_code(401);

            // показать сообщение об ошибке
            echo json_encode(array("message" => "Токен устарел, невозможно создать тип мест."));
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
