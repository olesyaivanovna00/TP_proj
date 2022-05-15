<?php
// заголовки
header("Access-Control-Allow-Origin: http://SOK/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// файлы необходимые для соединения с БД
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
$administrator_sites->mail = $data->mail;
$email_exists = $administrator_sites->emailExists();

// подключение файлов jwt
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

// существует ли электронная почта и соответствует ли пароль тому, что находится в базе данных
if ($email_exists && password_verify($data->password, $administrator_sites->password)) {

    $token = array(
        "iss" => $iss,
        "sub" => $subA,
        "aud" => $aud,
        "iat" => $iat,
        "data" => array(
            "id_administrator_sites" => $administrator_sites->id_administrator_sites,
            "login" => $administrator_sites->login,
            "mail" => $administrator_sites->mail
        )
    );

    // устанавливаем значения iat для администратора площадок 
    $administrator_sites->iat = $iat;
    $updateIAT = $administrator_sites->updateIAT();

    if ($updateIAT) {
        // код ответа
        http_response_code(200);

        // создание jwt
        $jwt = JWT::encode($token, $key, 'HS256');


        echo json_encode(
            array(
                "message" => "Успешный вход в систему.",
                "jwt" => $jwt
            )
        );
    }

    // сообщение, если не удается обновить администратора площадок
    else {
        // код ответа
        http_response_code(401);

        // показать сообщение об ошибке
        echo json_encode(array("message" => "Невозможно обновить вход администратора площадок."));
    }
}

// Если электронная почта не существует или пароль не совпадает,
// сообщим администратору площадок, что он не может войти в систему
else {

    // код ответа
    http_response_code(401);

    // сказать администратору площадок что войти не удалось
    echo json_encode(array("message" => "Ошибка входа."));
}
