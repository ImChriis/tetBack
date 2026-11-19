<?php
require_once __DIR__ . '/../../core/db.php';

const UPLOADS_DIR = __DIR__ . '/../../uploads';

const TRABAJO_SELECT = 'SELECT t.id, t.annio AS anio, t.titulo, t.autores, t.tutores, t.palabras_claves,
    t.id_carrera AS carrera_id, c.carrera, f.id AS facultad_id, f.siglas, f.facultad,
    t.resumen IS NOT NULL AS tiene_resumen, t.tesis IS NOT NULL AS tiene_tesis
    FROM tet t
    JOIN carreras c ON c.id = t.id_carrera
    JOIN facultades f ON f.id = c.facultad_id';

function filaTrabajo(array $r): array
{
    $r['anio'] = (int) $r['anio'];
    $r['tiene_resumen'] = (bool) $r['tiene_resumen'];
    $r['tiene_tesis'] = (bool) $r['tiene_tesis'];
    return $r;
}

function listarTrabajos(array $f): array
{
    $where = [];
    $params = [];
    if ($q = trim($f['q'] ?? '')) {
        $where[] = '(t.titulo LIKE ? OR t.autores LIKE ? OR t.tutores LIKE ? OR t.palabras_claves LIKE ?)';
        array_push($params, ...array_fill(0, 4, "%$q%"));
    }
    if ($anio = filter_var($f['anio'] ?? null, FILTER_VALIDATE_INT)) {
        $where[] = 't.annio = ?';
        $params[] = $anio;
    }
    if ($carrera = filter_var($f['carrera_id'] ?? null, FILTER_VALIDATE_INT)) {
        $where[] = 't.id_carrera = ?';
        $params[] = $carrera;
    }
    if ($facultadId = filter_var($f['facultad_id'] ?? null, FILTER_VALIDATE_INT)) {
        $where[] = 'f.id = ?';
        $params[] = $facultadId;
    }
    if ($facultad = trim($f['facultad'] ?? '')) {
        $where[] = 'f.siglas = ?';
        $params[] = $facultad;
    }
    if ($linea = filter_var($f['linea_id'] ?? null, FILTER_VALIDATE_INT)) {
        $where[] = 'EXISTS (SELECT 1 FROM tet_lineas tl WHERE tl.tet_id = t.id AND tl.linea_id = ?)';
        $params[] = $linea;
    }
    $sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

    $limit = min(max((int) ($f['limit'] ?? 12), 1), 50);
    $page = max((int) ($f['page'] ?? 1), 1);
    $offset = ($page - 1) * $limit;

    $st = db()->prepare('SELECT COUNT(*) FROM tet t JOIN carreras c ON c.id = t.id_carrera JOIN facultades f ON f.id = c.facultad_id' . $sqlWhere);
    $st->execute($params);
    $total = (int) $st->fetchColumn();

    // $limit y $offset son enteros ya validados
    $st = db()->prepare(TRABAJO_SELECT . $sqlWhere . " ORDER BY t.annio DESC, t.id DESC LIMIT $limit OFFSET $offset");
    $st->execute($params);

    return ['data' => array_map('filaTrabajo', $st->fetchAll()), 'total' => $total, 'page' => $page, 'limit' => $limit];
}

function obtenerTrabajo(int $id): ?array
{
    $st = db()->prepare(TRABAJO_SELECT . ' WHERE t.id = ?');
    $st->execute([$id]);
    $t = $st->fetch();
    if (!$t) {
        return null;
    }
    $st = db()->prepare('SELECT l.id, l.nombre_linea, l.eje_tematico FROM tet_lineas tl JOIN lineas_investigacion l ON l.id = tl.linea_id WHERE tl.tet_id = ?');
    $st->execute([$id]);
    return filaTrabajo($t) + ['lineas' => $st->fetchAll()];
}

// Las cédulas son datos personales: solo se piden desde el panel autenticado
function autoresDeTrabajo(int $id): array
{
    $st = db()->prepare('SELECT nombre, apellido, cedula FROM tet_autores WHERE tet_id = ? ORDER BY orden, id');
    $st->execute([$id]);
    return $st->fetchAll();
}

function trabajoCrudo(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM tet WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function lineasExistentes(array $ids): array
{
    if (!$ids) {
        return [];
    }
    $st = db()->prepare('SELECT id FROM lineas_investigacion WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')');
    $st->execute($ids);
    return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
}

function guardarTrabajo(?int $id, array $d, array $lineas, array $autores): int
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $valores = [$d['anio'], $d['titulo'], $d['autores'], $d['tutores'], $d['tutor']['nombre'], $d['tutor']['apellido'], $d['tutor']['cedula'],
            $d['palabras_claves'], $d['resumen'], $d['tesis'], $d['carrera_id']];
        if ($id) {
            $pdo->prepare('UPDATE tet SET annio = ?, titulo = ?, autores = ?, tutores = ?, tutor_nombre = ?, tutor_apellido = ?, tutor_cedula = ?,
                palabras_claves = ?, resumen = ?, tesis = ?, id_carrera = ? WHERE id = ?')
                ->execute([...$valores, $id]);
        } else {
            $pdo->prepare('INSERT INTO tet (annio, titulo, autores, tutores, tutor_nombre, tutor_apellido, tutor_cedula, palabras_claves, resumen, tesis, id_carrera)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
                ->execute($valores);
            $id = (int) $pdo->lastInsertId();
        }
        $pdo->prepare('DELETE FROM tet_autores WHERE tet_id = ?')->execute([$id]);
        $st = $pdo->prepare('INSERT INTO tet_autores (tet_id, nombre, apellido, cedula, orden) VALUES (?, ?, ?, ?, ?)');
        foreach ($autores as $i => $a) {
            $st->execute([$id, $a['nombre'], $a['apellido'], $a['cedula'], $i]);
        }
        $pdo->prepare('DELETE FROM tet_lineas WHERE tet_id = ?')->execute([$id]);
        $st = $pdo->prepare('INSERT INTO tet_lineas (tet_id, linea_id) VALUES (?, ?)');
        foreach ($lineas as $linea) {
            $st->execute([$id, $linea]);
        }
        $pdo->commit();
        return $id;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function eliminarTrabajo(int $id): void
{
    db()->prepare('DELETE FROM tet WHERE id = ?')->execute([$id]);
}

// Las filas viejas guardan rutas tipo "../../assest/uploads/x.pdf": solo cuenta el nombre del archivo
function rutaPdf(?string $valor): ?string
{
    return $valor ? UPLOADS_DIR . '/' . basename($valor) : null;
}

// Imagen en caché de la primera página; el nombre cambia si el PDF se reemplaza
function rutaPortada(string $rutaPdf): string
{
    return UPLOADS_DIR . '/portadas/' . sha1(basename($rutaPdf) . filemtime($rutaPdf)) . '.png';
}

// Varios trabajos viejos comparten el mismo PDF: solo se borra si ya nadie lo usa
function borrarPdfSiHuerfano(?string $valor): void
{
    if (!$valor) {
        return;
    }
    $st = db()->prepare('SELECT COUNT(*) FROM tet WHERE resumen = ? OR tesis = ?');
    $st->execute([$valor, $valor]);
    $ruta = rutaPdf($valor);
    if ((int) $st->fetchColumn() === 0 && is_file($ruta)) {
        @unlink(rutaPortada($ruta));
        unlink($ruta);
    }
}
