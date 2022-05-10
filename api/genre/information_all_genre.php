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

// запрашиваем жанры
$stmt = $genre->information_all_genre();
$num = $stmt->rowCount();

// проверка, найдено ли больше 0 записей
if ($num > 0) {

    // массив жанров
    $genre_arr = array();
    $genre_arr["genre_records"] = array();

    // получаем содержимое нашей таблицы
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // извлекаем строку
        extract($row);

        $genre_item = array(
            "id_genre" => $id_genre,
            "title" => $title
        );

        array_push($genre_arr["genre_records"], $genre_item);
    }

    // код ответа
    http_response_code(200);

    // показать детали
    echo json_encode($genre_arr);
}

// показать сообщение об отсутствующих жанрах
else {

    // код ответа
    http_response_code(404);

    // сообщаем, что жанры не найдены
    echo json_encode(array("message" => "Жанры не найдены."), JSON_UNESCAPED_UNICODE);
}
