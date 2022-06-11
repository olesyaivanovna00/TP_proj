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

// подключение к БД
// файлы, необходимые для подключения к базе данных
include_once '../config/database.php';
include_once '../objects/ticket.php';
include_once '../objects/types_places.php';
include_once '../objects/organizer.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Ticket'
$ticket = new Ticket($db);

// создание объекта 'Place_hall'
$place_hall = new Place_hall($db);

// создание объекта 'Organizer'
$organizer = new Organizer($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// получаем jwt
$jwt = isset($data->jwt) ? $data->jwt : "";

// если JWT не пуст
if ($jwt) {

    // если декодирование выполнено успешно, показать данные концерта
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

            // получаем id_types_places
            $id_types_places = isset($data->id_types_places) ? $data->id_types_places : "";
            $place_hall->id_types_places = $id_types_places;

            // запрашиваем места определенного типа мест
            $stmt = $place_hall->information_all_place_hall_types_places();
            $num = $stmt->rowCount();

            // проверка, найдено ли больше 0 записей
            if ($num > 0) {

                // получаем содержимое нашей таблицы
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    // извлекаем строку
                    extract($row);

                    $ticket->id_place_hall = $id_place_hall;

                    // Нам нужно установить отправленные данные (через форму HTML) в свойствах объекта билет
                    $ticket->id_concert = $data->$id_concert;
                    $ticket->status = $data->status;
                    $ticket->price = $data->$price;

                    // создание билета
                    if (
                        !empty($ticket->id_concert) &&
                        !empty($ticket->status) &&
                        !empty($ticket->price) &&
                        !empty($ticket->id_place_hall)
                    ) {

                        // если все параметры переданы, создаем билет
                        if ($ticket->create()) {
                        }
                    }

                    // сообщение, если не удаётся создать билет
                    else {

                        // устанавливаем код ответа
                        http_response_code(400);

                        // покажем сообщение о том, что создать билет не удалось
                        echo json_encode(array("message" => "Невозможно создать билет, не хватает параметров"));
                    }
                }
            }

            // показать сообщение об отсутствующих местах
            else {

                // код ответа
                http_response_code(404);

                // сообщаем, что места не найдены
                echo json_encode(array("message" => "Места не найдены."), JSON_UNESCAPED_UNICODE);
            }
        }

        // сообщение, если iat для организатора устарел
        else {
            // код ответа
            http_response_code(401);

            // показать сообщение об ошибке
            echo json_encode(array("message" => "Токен устарел, невозможно создать билет."));
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
