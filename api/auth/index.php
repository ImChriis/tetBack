<?php
require_once __DIR__ . '/../../core/auth.php';

// Las contraseñas viejas de tetv1 están en sha1: se aceptan una vez y se pasan a bcrypt
function verificarClave(string $clave, array $u): bool
{
    if (password_verify($clave, $u['contrasena'])) {
        return true;
    }
    if (strlen($u['contrasena']) === 40 && hash_equals($u['contrasena'], sha1($clave))) {
        db()->prepare('UPDATE usuarios SET contrasena = ? WHERE id = ?')
            ->execute([password_hash($clave, PASSWORD_DEFAULT), $u['id']]);
        return true;
    }
    return false;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $d = body();
        requireFields($d, ['usuario', 'contrasena']);
        $u = buscarUsuario('usuario', trim($d['usuario']));
        if (!$u || !verificarClave((string) $d['contrasena'], $u)) {
            json(['error' => 'Usuario o contraseña incorrectos'], 401);
        }
        json(['token' => jwtCrear((int) $u['id']), 'usuario' => usuarioPublico($u)]);
    case 'GET':
        json(requireAuth());
    default:
        metodoNoPermitido();
}
