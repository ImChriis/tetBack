<?php
require_once __DIR__ . '/../../core/http.php';
require_once __DIR__ . '/../../core/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    metodoNoPermitido();
}
$r = db()->query('SELECT
    (SELECT COUNT(*) FROM tet) AS trabajos,
    (SELECT COUNT(*) FROM facultades) AS facultades,
    (SELECT COUNT(*) FROM carreras) AS carreras,
    (SELECT MIN(annio) FROM tet) AS primer_anio')->fetch();
$anios = db()->query('SELECT DISTINCT annio FROM tet ORDER BY annio DESC')->fetchAll(PDO::FETCH_COLUMN);

json(array_map(fn($v) => $v === null ? null : (int) $v, $r) + ['anios' => array_map('intval', $anios)]);
