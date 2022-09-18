<?php
// показывать сообщения об ошибках
ini_set('display_errors', 1);
error_reporting(E_ALL);

// URL домашней страницы
$home_url = "http://localhost/api/";

// установить часовой пояс по умолчанию
date_default_timezone_set('Europe/Moscow');

// переменные, используемые для JWT
$key = "secret_key";
$iss = "SOK";
$subU = "authU";
$subA = "authA";
$subO = "authO";
$aud = "http://SOK/";
$iat = time();
