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
include_once '../objects/ticket.php';
include_once '../objects/users.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Ticket'
$ticket = new Ticket($db);

// создание объекта 'Users'
$users = new Users($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// получаем jwt
$jwt = isset($data->jwt) ? $data->jwt : "";

// если JWT не пуст
if ($jwt) {

    // если декодирование выполнено успешно, показать данные билета
    try {

        // декодирование jwt
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        // устанавливаем значения iat для пользователя 
        $users->id_users = $decoded->data->id_users;
        $users->iat = $decoded->iat;

        // устанавливаем значения sub для пользователя 
        $users->sub = $decoded->sub;
        $users->subCheck = $subU;

        //проверяем значения
        if ($users->checkIAT() && $users->checkSUB()) {

            // Нам нужно установить отправленные данные в свойствах объекта билет
            $ticket->id_ticket = $data->$id_ticket;
            //$ticket->status = $data->status;
            $ticket->id_users = $decoded->data->id_users;

            // обновление билета
            if (
                !empty($ticket->id_ticket) &&
                //!empty($ticket->status) &&
                !empty($ticket->id_users)
            ) {

                // если все параметры переданы, обновляем билет
                if ($ticket->purchase_ticket_users()) {

                    // устанавливаем код ответа
                    http_response_code(200);

                    // покажем сообщение о том, что билет куплен
                    echo json_encode(array("message" => "Билет куплен."));
                }
            }

            // сообщение, если не удаётся купить билет
            else {

                // устанавливаем код ответа
                http_response_code(400);

                // покажем сообщение о том, что купить билет не удалось
                echo json_encode(array("message" => "Невозможно купить билет, не хватает параметров"));
            }
        }

        // сообщение, если iat для пользователя устарел
        else {
            // код ответа
            http_response_code(401);

            // показать сообщение об ошибке
            echo json_encode(array("message" => "Токен устарел, невозможно купить билет."));
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

    // сообщить пользователю что доступ запрещен
    echo json_encode(array("message" => "Доступ закрыт."));
}
