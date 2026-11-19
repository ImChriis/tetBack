<?php
require_once __DIR__ . '/../config/config.php';

function db(): PDO
{
    static $pdo = null;
    return $pdo ??= new PDO(
        'mysql:host=' . CONFIG['db_host'] . ';dbname=' . CONFIG['db_name'] . ';charset=utf8mb4',
        CONFIG['db_user'],
        CONFIG['db_pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}
