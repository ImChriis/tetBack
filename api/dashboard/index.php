<?php
require_once __DIR__ . '/../../core/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    metodoNoPermitido();
}
$usuario = requireAuth();

// Un decano ve solo su facultad; el administrador ve todo
$where = $usuario['es_admin'] ? '' : ' WHERE c.facultad_id = ?';
$params = $usuario['es_admin'] ? [] : [$usuario['facultad_id']];
$from = ' FROM tet t JOIN carreras c ON c.id = t.id_carrera JOIN facultades f ON f.id = c.facultad_id';

function consulta(string $sql, array $params): array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

$totales = consulta('SELECT COUNT(*) AS trabajos, COUNT(t.resumen) AS con_resumen, COUNT(t.tesis) AS con_tesis, COUNT(CASE WHEN t.resumen IS NOT NULL AND t.tesis IS NOT NULL THEN 1 END) AS completos' . $from . $where, $params)[0];
$totales['carreras'] = (int) consulta('SELECT COUNT(*) AS n FROM carreras c' . $where, $params)[0]['n'];
$totales['lineas'] = (int) db()->query('SELECT COUNT(*) FROM lineas_investigacion')->fetchColumn();

$porAnio = consulta('SELECT t.annio AS anio, COUNT(*) AS total' . $from . $where . ' GROUP BY t.annio ORDER BY t.annio', $params);
$porFacultad = consulta('SELECT f.siglas, f.facultad, COUNT(t.id) AS total FROM facultades f
    LEFT JOIN carreras c ON c.facultad_id = f.id LEFT JOIN tet t ON t.id_carrera = c.id'
    . ($usuario['es_admin'] ? '' : ' WHERE f.id = ?') . ' GROUP BY f.id ORDER BY total DESC', $params);
$porCarrera = consulta('SELECT c.carrera, f.siglas, COUNT(*) AS total' . $from . $where . ' GROUP BY c.id ORDER BY total DESC LIMIT 6', $params);

// Las palabras clave se guardan como texto separado por comas
$conteo = [];
foreach (consulta('SELECT t.palabras_claves' . $from . $where, $params) as $fila) {
    foreach (array_filter(array_map('trim', explode(',', $fila['palabras_claves']))) as $p) {
        $clave = mb_strtolower($p);
        $conteo[$clave] = ($conteo[$clave] ?? 0) + 1;
    }
}
arsort($conteo);
$palabras = array_map(fn($p, $n) => ['palabra' => $p, 'total' => $n], array_keys($conteo), $conteo);

$pendientes = consulta('SELECT t.id, t.titulo, t.annio AS anio, f.siglas, t.resumen IS NULL AS falta_resumen, t.tesis IS NULL AS falta_tesis'
    . $from . ($where ?: ' WHERE 1') . ' AND (t.resumen IS NULL OR t.tesis IS NULL) ORDER BY t.id DESC LIMIT 6', $params);
$recientes = consulta('SELECT t.id, t.titulo, t.annio AS anio, c.carrera, f.siglas' . $from . $where . ' ORDER BY t.id DESC LIMIT 5', $params);

$enteros = fn(array $filas, array $campos) => array_map(function ($f) use ($campos) {
    foreach ($campos as $c) {
        $f[$c] = (int) $f[$c];
    }
    return $f;
}, $filas);

json([
    'totales' => array_map('intval', $totales),
    'por_anio' => $enteros($porAnio, ['anio', 'total']),
    'por_facultad' => $enteros($porFacultad, ['total']),
    'por_carrera' => $enteros($porCarrera, ['total']),
    'palabras' => array_slice($palabras, 0, 8),
    'pendientes' => array_map(fn($p) => ['falta_resumen' => (bool) $p['falta_resumen'], 'falta_tesis' => (bool) $p['falta_tesis']] + $p, $enteros($pendientes, ['id', 'anio'])),
    'recientes' => $enteros($recientes, ['id', 'anio']),
]);
