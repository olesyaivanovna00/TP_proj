<?php
// заголовки
header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// файлы необходимые для соединения с БД
include_once '../config/database.php';
include_once '../objects/user.php';
 
// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();
 
// создание объекта 'User'
$users = new Users($db);
 
// получаем данные
$data = json_decode(file_get_contents("php://input"));
 
// устанавливаем значения
$users->mail = $data->mail;
$email_exists = $users->emailExists();
 
// файлы для JWT будут здесь

?>