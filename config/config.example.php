<?php
// Copiar como config.php y completar. config.php no se sube a git.
const CONFIG = [
    'db_host' => 'localhost',
    'db_name' => 'trabajo_especial_titulacion',
    'db_user' => 'root',
    'db_pass' => '',
    'jwt_secret' => 'cambiar-por-una-cadena-larga-y-aleatoria',
    'jwt_ttl' => 8 * 3600,
    'cors_origin' => 'http://localhost:4200',
    'max_pdf_bytes' => 20 * 1024 * 1024,
    'rol_admin' => 4,
    // rol de decano => facultad que puede gestionar
    'rol_facultad' => [1 => 1, 2 => 2, 3 => 3],
];
