<?php
// заголовки
header("Access-Control-Allow-Origin: http://SOK/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// файлы необходимые для соединения с БД
include_once '../config/database.php';
include_once '../objects/organizer.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Organizer'
$organizer = new Organizer($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// устанавливаем значения
$organizer->mail = $data->mail;
$email_exists = $organizer->emailExists();

// подключение файлов jwt
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

// существует ли электронная почта и соответствует ли пароль тому, что находится в базе данных
if ($email_exists && password_verify($data->password, $organizer->password)) {

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

    // устанавливаем значения iat для организатора 
    $organizer->iat = $iat;
    $updateIAT = $organizer->updateIAT();

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

// Если электронная почта не существует или пароль не совпадает,
// сообщим организатору, что он не может войти в систему
else {

    // код ответа
    http_response_code(401);

    // сказать организатору что войти не удалось
    echo json_encode(array("message" => "Ошибка входа."));
}
