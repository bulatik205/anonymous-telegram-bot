<?php
$token = ""; # you Telegram Bot API Token
$main_host = "localhost"; # MySQL host
$main_user = ""; # MySQL user
$main_pass = ""; # MySQL password
$main_db = ""; # MySQL database

$main_pdo = new PDO(
    "mysql:host=$main_host;dbname=$main_db;charset=utf8mb4",
    $main_user,
    $main_pass, 
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);
?>