<?php
require_once __DIR__ . '/../../core/auth.php';

// Catálogos simples: tabla, campos editables (con largo máximo), orden y qué los usa
const CATALOGOS = [
    'facultades' => [
        'tabla' => 'facultades',
        'campos' => ['facultad' => 100, 'siglas' => 20],
        'orden' => 'facultad',
        'en_uso' => 'SELECT COUNT(*) FROM carreras WHERE facultad_id = ?',
    ],
    'carreras' => [
        'tabla' => 'carreras',
        'campos' => ['carrera' => 100, 'facultad_id' => 0],
        'orden' => 'carrera',
        'en_uso' => 'SELECT COUNT(*) FROM tet WHERE id_carrera = ?',
    ],
    'lineas' => [
        'tabla' => 'lineas_investigacion',
        'campos' => ['nombre_linea' => 255, 'eje_tematico' => 255],
        'orden' => 'nombre_linea',
        'en_uso' => null,
    ],
    // Los roles están atados a CONFIG['rol_facultad']: solo lectura
    'roles' => ['tabla' => 'roles', 'campos' => [], 'orden' => 'id', 'en_uso' => null],
];

$cat = CATALOGOS[$_GET['tipo'] ?? ''] ?? json(['error' => 'Catálogo no válido'], 404);
$tabla = $cat['tabla'];

function datosCatalogo(array $cat): array
{
    $d = body();
    requireFields($d, array_keys($cat['campos']));
    $valores = [];
    foreach ($cat['campos'] as $campo => $max) {
        if ($max === 0) {
            $valores[$campo] = filter_var($d[$campo], FILTER_VALIDATE_INT) ?: json(['error' => "\"$campo\" inválido"], 400);
            continue;
        }
        $valores[$campo] = trim($d[$campo]);
        if (mb_strlen($valores[$campo]) > $max) {
            json(['error' => "\"$campo\" admite máximo $max caracteres"], 400);
        }
    }
    if (isset($valores['facultad_id'])) {
        $st = db()->prepare('SELECT 1 FROM facultades WHERE id = ?');
        $st->execute([$valores['facultad_id']]);
        $st->fetchColumn() ?: json(['error' => 'La facultad no existe'], 400);
    }
    return $valores;
}

function existe(string $tabla, int $id): void
{
    $st = db()->prepare("SELECT 1 FROM $tabla WHERE id = ?");
    $st->execute([$id]);
    $st->fetchColumn() ?: json(['error' => 'Registro no encontrado'], 404);
}

$metodo = $_SERVER['REQUEST_METHOD'];
if ($metodo !== 'GET') {
    requireAdmin();
    if (!$cat['campos']) {
        json(['error' => 'Este catálogo es de solo lectura'], 405);
    }
}

switch ($metodo) {
    case 'GET':
        if ($tabla === 'carreras' && ($facultad = filter_var($_GET['facultad_id'] ?? null, FILTER_VALIDATE_INT))) {
            $st = db()->prepare('SELECT * FROM carreras WHERE facultad_id = ? ORDER BY carrera');
            $st->execute([$facultad]);
            json($st->fetchAll());
        }
        json(db()->query("SELECT * FROM $tabla ORDER BY {$cat['orden']}")->fetchAll());
    case 'POST':
        $v = datosCatalogo($cat);
        $columnas = implode(', ', array_keys($v));
        $marcas = implode(', ', array_fill(0, count($v), '?'));
        db()->prepare("INSERT INTO $tabla ($columnas) VALUES ($marcas)")->execute(array_values($v));
        json(['id' => (int) db()->lastInsertId()] + $v, 201);
    case 'PUT':
        $id = idParam();
        existe($tabla, $id);
        $v = datosCatalogo($cat);
        $set = implode(', ', array_map(fn($c) => "$c = ?", array_keys($v)));
        db()->prepare("UPDATE $tabla SET $set WHERE id = ?")->execute([...array_values($v), $id]);
        json(['id' => $id] + $v);
    case 'DELETE':
        $id = idParam();
        existe($tabla, $id);
        if ($cat['en_uso']) {
            $st = db()->prepare($cat['en_uso']);
            $st->execute([$id]);
            if ((int) $st->fetchColumn() > 0) {
                json(['error' => 'No se puede eliminar: está en uso'], 409);
            }
        }
        db()->prepare("DELETE FROM $tabla WHERE id = ?")->execute([$id]);
        json(['ok' => true]);
    default:
        metodoNoPermitido();
}
