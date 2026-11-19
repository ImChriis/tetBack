<?php
require_once __DIR__ . '/../../core/auth.php';
require_once __DIR__ . '/repo.php';

const LIMITES = ['titulo' => 255, 'palabras_claves' => 140];
const MAX_AUTORES = 10;

function guardarPdf(string $campo): ?string
{
    $f = $_FILES[$campo] ?? null;
    if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > CONFIG['max_pdf_bytes']) {
        json(['error' => "No se pudo subir el archivo \"$campo\" o supera el tamaño máximo"], 400);
    }
    if ((new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']) !== 'application/pdf') {
        json(['error' => "El archivo \"$campo\" debe ser PDF"], 400);
    }
    $nombre = bin2hex(random_bytes(16)) . '.pdf';
    if (!move_uploaded_file($f['tmp_name'], UPLOADS_DIR . '/' . $nombre)) {
        throw new RuntimeException("No se pudo guardar el PDF $campo");
    }
    return $nombre;
}

// Autores y tutor comparten reglas: nombre y apellido obligatorios, cédula venezolana
function leerPersona(array $p, string $quien): array
{
    $persona = [
        'nombre' => trim((string) ($p['nombre'] ?? '')),
        'apellido' => trim((string) ($p['apellido'] ?? '')),
        'cedula' => strtoupper(str_replace(['.', ' '], '', trim((string) ($p['cedula'] ?? '')))),
    ];
    if ($persona['nombre'] === '' || $persona['apellido'] === '' || mb_strlen($persona['nombre']) > 50 || mb_strlen($persona['apellido']) > 50) {
        json(['error' => "$quien: nombre y apellido son obligatorios (máximo 50 caracteres)"], 400);
    }
    if (!preg_match('/^([VE]-?)?\d{6,10}$/', $persona['cedula'])) {
        json(['error' => "$quien: cédula inválida (ej. 12345678 o V-12345678)"], 400);
    }
    return $persona;
}

function soloDigitos(string $cedula): string
{
    return preg_replace('/\D/', '', $cedula);
}

// Llegan como JSON en el campo "autores": [{nombre, apellido, cedula}, ...]
function leerAutores(string $json): array
{
    $lista = json_decode($json, true);
    if (!is_array($lista) || !$lista || !array_is_list($lista)) {
        json(['error' => 'Agrega al menos un autor'], 400);
    }
    if (count($lista) > MAX_AUTORES) {
        json(['error' => 'Máximo ' . MAX_AUTORES . ' autores por trabajo'], 400);
    }
    $autores = array_map(fn($a, $i) => leerPersona(is_array($a) ? $a : [], 'Autor ' . ($i + 1)), $lista, array_keys($lista));
    $cedulas = array_map(fn($a) => soloDigitos($a['cedula']), $autores);
    if (count(array_unique($cedulas)) !== count($cedulas)) {
        json(['error' => 'Hay cédulas repetidas entre los autores'], 400);
    }
    return $autores;
}

// multipart/form-data: PHP no llena $_FILES en PUT, por eso crear y editar van por POST
function guardar(array $usuario, ?int $id): array
{
    $d = $_POST;
    requireFields($d, ['anio', 'titulo', 'autores', 'tutor_nombre', 'tutor_apellido', 'tutor_cedula', 'palabras_claves', 'carrera_id']);
    $autores = leerAutores($d['autores']);
    $tutor = leerPersona(['nombre' => $d['tutor_nombre'], 'apellido' => $d['tutor_apellido'], 'cedula' => $d['tutor_cedula']], 'Tutor');
    if (in_array(soloDigitos($tutor['cedula']), array_map(fn($a) => soloDigitos($a['cedula']), $autores), true)) {
        json(['error' => 'El tutor no puede ser también autor del trabajo'], 400);
    }
    // Texto de autores que usan la búsqueda y las tarjetas públicas
    $d['autores'] = implode(', ', array_map(fn($a) => "{$a['nombre']} {$a['apellido']}", $autores));
    if (mb_strlen($d['autores']) > 255) {
        json(['error' => 'Los nombres de los autores juntos superan 255 caracteres'], 400);
    }
    foreach (LIMITES as $campo => $max) {
        $d[$campo] = trim($d[$campo]);
        if (mb_strlen($d[$campo]) > $max) {
            json(['error' => "\"$campo\" admite máximo $max caracteres"], 400);
        }
    }
    $anio = filter_var($d['anio'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1901, 'max_range' => (int) date('Y') + 1]]);
    $carrera = filter_var($d['carrera_id'], FILTER_VALIDATE_INT);
    if (!$anio || !$carrera) {
        json(['error' => 'Año o carrera inválidos'], 400);
    }
    $st = db()->prepare('SELECT 1 FROM carreras WHERE id = ?');
    $st->execute([$carrera]);
    if (!$st->fetchColumn()) {
        json(['error' => 'La carrera no existe'], 400);
    }

    $actual = null;
    if ($id) {
        $actual = trabajoCrudo($id) ?? json(['error' => 'Trabajo no encontrado'], 404);
        if (!puedeGestionarCarrera($usuario, (int) $actual['id_carrera'])) {
            json(['error' => 'Sin permiso sobre este trabajo'], 403);
        }
    }
    if (!puedeGestionarCarrera($usuario, $carrera)) {
        json(['error' => 'Sin permiso sobre esta carrera'], 403);
    }

    $lineas = lineasExistentes(array_values(array_unique(array_filter(array_map('intval', (array) ($d['lineas'] ?? []))))));

    $resumen = guardarPdf('resumen');
    $tesis = guardarPdf('tesis');
    try {
        $nuevoId = guardarTrabajo($id, [
            'anio' => $anio,
            'titulo' => $d['titulo'],
            'autores' => $d['autores'],
            'tutores' => "{$tutor['nombre']} {$tutor['apellido']}",
            'tutor' => $tutor,
            'palabras_claves' => $d['palabras_claves'],
            'resumen' => $resumen ?? $actual['resumen'] ?? null,
            'tesis' => $tesis ?? $actual['tesis'] ?? null,
            'carrera_id' => $carrera,
        ], $lineas, $autores);
    } catch (Throwable $e) {
        foreach ([$resumen, $tesis] as $nuevo) {
            if ($nuevo) {
                unlink(UPLOADS_DIR . '/' . $nuevo);
            }
        }
        throw $e;
    }

    if ($actual && $resumen) {
        borrarPdfSiHuerfano($actual['resumen']);
    }
    if ($actual && $tesis) {
        borrarPdfSiHuerfano($actual['tesis']);
    }
    return obtenerTrabajo($nuevoId);
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = idParam();
            $t = obtenerTrabajo($id) ?? json(['error' => 'Trabajo no encontrado'], 404);
            $usuario = usuarioOpcional();
            if ($usuario && puedeGestionarCarrera($usuario, $t['carrera_id'])) {
                $t['autores_detalle'] = autoresDeTrabajo($id);
                $crudo = trabajoCrudo($id);
                $t['tutor_detalle'] = $crudo['tutor_cedula'] === null ? null
                    : ['nombre' => $crudo['tutor_nombre'], 'apellido' => $crudo['tutor_apellido'], 'cedula' => $crudo['tutor_cedula']];
            }
            json($t);
        }
        json(listarTrabajos($_GET));
    case 'POST':
        $usuario = requireAuth();
        $id = isset($_GET['id']) ? idParam() : null;
        json(guardar($usuario, $id), $id ? 200 : 201);
    case 'DELETE':
        $usuario = requireAuth();
        $id = idParam();
        $t = trabajoCrudo($id) ?? json(['error' => 'Trabajo no encontrado'], 404);
        if (!puedeGestionarCarrera($usuario, (int) $t['id_carrera'])) {
            json(['error' => 'Sin permiso sobre este trabajo'], 403);
        }
        eliminarTrabajo($id);
        borrarPdfSiHuerfano($t['resumen']);
        borrarPdfSiHuerfano($t['tesis']);
        json(['ok' => true]);
    default:
        metodoNoPermitido();
}
