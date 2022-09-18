<?php
// заголовки
header("Access-Control-Allow-Origin: http://SOK/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// подключение к БД
// файлы, необходимые для подключения к базе данных
include_once '../config/database.php';
include_once '../objects/genre.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'Genre'
$genre = new Genre($db);

// получаем значение JSON
$data = json_decode(file_get_contents("php://input"));

// получаем id_genre
$id_genre = isset($data->id_genre) ? $data->id_genre : "";
$genre->id_genre = $id_genre;

//есть ли в базе информация о жанре
$information_data = $genre->information_genre();

if ($information_data) {

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode(
        array(
            "id_genre" => $genre->id_genre,
            "title" => $genre->title
        )
    );
}

// показать сообщение об отсутствующем жанре
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что жанр не найден
    echo json_encode(array("message" => "Жанр не найден."), JSON_UNESCAPED_UNICODE);
}
