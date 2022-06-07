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
include_once '../objects/concert.php';
include_once '../objects/organizer.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Concert'
$concert = new Concert($db);

// создание объекта 'Organizer'
$organizer = new Organizer($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// получаем jwt
$jwt = isset($data->jwt) ? $data->jwt : "";

// если JWT не пуст
if ($jwt) {

    // если декодирование выполнено успешно, показать данные промо концерта
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
            // Нам нужно установить отправленные данные (через форму HTML) в свойствах объекта концерт
            $concert->id_concert = $data->id_concert;
            $concert->description_promo = $data->description_promo;

            //если новая картинка не загружена
            if ($_FILES['picture']['tmp_name'] == "") {

                //оставляем путь к старой картинке
                $concert->img_promo = $data->img_promo;
            }

            //если новая картинка загружена
            else {

                // Путь загрузки
                $path = '../../image/';

                //время загрузки
                $timeImg = time();

                //путь к новой картинке
                $concert->img_promo = $path . $timeImg . '+' . $_FILES['picture']['name'];
                @copy($_FILES['picture']['tmp_name'], $path . $timeImg . '+' . $_FILES['picture']['name']);
            }

            // обновление промо концерта
            if ($concert->update_promo()) {

                // устанавливаем код ответа
                http_response_code(200);

                // покажем сообщение о том, что промо концерта обновлено
                echo json_encode(array("message" => "Промо концерта обновлено."));
            }

            // сообщение, если не удается обновить промо концерта
            else {
                // код ответа
                http_response_code(401);

                // показать сообщение об ошибке
                echo json_encode(array("message" => "Невозможно обновить промо концерта."));
            }
        }
        // сообщение, если iat для организатора устарел
        else {
            // код ответа
            http_response_code(401);

            // показать сообщение об ошибке
            echo json_encode(array("message" => "Токен устарел, невозможно обновить промо концерта."));
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
