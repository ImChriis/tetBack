<?php
require_once __DIR__ . '/../../core/auth.php';

const USUARIO_SELECT = 'SELECT u.id, u.nombre, u.apellido, u.cedula, u.correo, u.usuario, u.rol_id, r.rol
    FROM usuarios u JOIN roles r ON r.id = u.rol_id';
const LIMITES = ['nombre' => 50, 'apellido' => 50, 'cedula' => 13, 'correo' => 100, 'usuario' => 20];

function obtenerUsuario(int $id): ?array
{
    $st = db()->prepare(USUARIO_SELECT . ' WHERE u.id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

// La contraseña es obligatoria al crear y opcional al editar
function datosUsuario(?int $id): array
{
    $d = body();
    requireFields($d, [...array_keys(LIMITES), 'rol_id', ...($id ? [] : ['contrasena'])]);
    $v = [];
    foreach (LIMITES as $campo => $max) {
        $v[$campo] = trim($d[$campo]);
        if (mb_strlen($v[$campo]) > $max) {
            json(['error' => "\"$campo\" admite máximo $max caracteres"], 400);
        }
    }
    if (!filter_var($v['correo'], FILTER_VALIDATE_EMAIL)) {
        json(['error' => 'Correo inválido'], 400);
    }
    $v['rol_id'] = filter_var($d['rol_id'], FILTER_VALIDATE_INT) ?: json(['error' => 'Rol inválido'], 400);
    $st = db()->prepare('SELECT 1 FROM roles WHERE id = ?');
    $st->execute([$v['rol_id']]);
    $st->fetchColumn() ?: json(['error' => 'El rol no existe'], 400);

    $st = db()->prepare('SELECT 1 FROM usuarios WHERE usuario = ? AND id <> ?');
    $st->execute([$v['usuario'], $id ?? 0]);
    if ($st->fetchColumn()) {
        json(['error' => 'El nombre de usuario ya existe'], 409);
    }

    $clave = (string) ($d['contrasena'] ?? '');
    if ($clave !== '') {
        if (strlen($clave) < 8) {
            json(['error' => 'La contraseña debe tener al menos 8 caracteres'], 400);
        }
        $v['contrasena'] = password_hash($clave, PASSWORD_DEFAULT);
    }
    return $v;
}

$yo = requireAdmin();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            json(obtenerUsuario(idParam()) ?? json(['error' => 'Usuario no encontrado'], 404));
        }
        json(db()->query(USUARIO_SELECT . ' ORDER BY u.nombre, u.apellido')->fetchAll());
    case 'POST':
        $v = datosUsuario(null);
        $columnas = implode(', ', array_keys($v));
        $marcas = implode(', ', array_fill(0, count($v), '?'));
        db()->prepare("INSERT INTO usuarios ($columnas) VALUES ($marcas)")->execute(array_values($v));
        json(obtenerUsuario((int) db()->lastInsertId()), 201);
    case 'PUT':
        $id = idParam();
        obtenerUsuario($id) ?? json(['error' => 'Usuario no encontrado'], 404);
        $v = datosUsuario($id);
        $set = implode(', ', array_map(fn($c) => "$c = ?", array_keys($v)));
        db()->prepare("UPDATE usuarios SET $set WHERE id = ?")->execute([...array_values($v), $id]);
        json(obtenerUsuario($id));
    case 'DELETE':
        $id = idParam();
        if ($id === $yo['id']) {
            json(['error' => 'No puedes eliminar tu propio usuario'], 409);
        }
        obtenerUsuario($id) ?? json(['error' => 'Usuario no encontrado'], 404);
        db()->prepare('DELETE FROM usuarios WHERE id = ?')->execute([$id]);
        json(['ok' => true]);
    default:
        metodoNoPermitido();
}
