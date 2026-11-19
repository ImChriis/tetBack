<?php
require_once __DIR__ . '/http.php';
require_once __DIR__ . '/db.php';

// shortcut: JWT HS256 hecho a mano porque no hay composer; si se instala, cambiar por firebase/php-jwt
function b64url(string $s): string
{
    return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
}

function jwtCrear(int $usuarioId): string
{
    $h = b64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $p = b64url(json_encode(['sub' => $usuarioId, 'exp' => time() + CONFIG['jwt_ttl']]));
    return "$h.$p." . b64url(hash_hmac('sha256', "$h.$p", CONFIG['jwt_secret'], true));
}

// Ignora el "alg" del header: siempre verifica HS256 con nuestra clave
function jwtLeer(string $token): ?array
{
    $partes = explode('.', $token);
    if (count($partes) !== 3) {
        return null;
    }
    [$h, $p, $firma] = $partes;
    if (!hash_equals(b64url(hash_hmac('sha256', "$h.$p", CONFIG['jwt_secret'], true)), $firma)) {
        return null;
    }
    $payload = json_decode(base64_decode(strtr($p, '-_', '+/')), true);
    return is_array($payload) && ($payload['exp'] ?? 0) > time() ? $payload : null;
}

function usuarioPublico(array $u): array
{
    $rolId = (int) $u['rol_id'];
    return [
        'id' => (int) $u['id'],
        'nombre' => $u['nombre'],
        'apellido' => $u['apellido'],
        'usuario' => $u['usuario'],
        'rol_id' => $rolId,
        'rol' => $u['rol'],
        'es_admin' => $rolId === CONFIG['rol_admin'],
        'facultad_id' => CONFIG['rol_facultad'][$rolId] ?? null,
    ];
}

function buscarUsuario(string $campo, string|int $valor): ?array
{
    $st = db()->prepare("SELECT u.*, r.rol FROM usuarios u JOIN roles r ON r.id = u.rol_id WHERE u.$campo = ?");
    $st->execute([$valor]);
    return $st->fetch() ?: null;
}

// Se lee el usuario en cada petición: un cambio de rol o un borrado aplica al instante
function usuarioOpcional(): ?array
{
    $cabecera = $_SERVER['HTTP_AUTHORIZATION'] ?? (getallheaders()['Authorization'] ?? '');
    $payload = preg_match('/^Bearer\s+(\S+)$/', $cabecera, $m) ? jwtLeer($m[1]) : null;
    $u = $payload ? buscarUsuario('id', (int) $payload['sub']) : null;
    return $u ? usuarioPublico($u) : null;
}

function requireAuth(): array
{
    return usuarioOpcional() ?? json(['error' => 'No autenticado'], 401);
}

function requireAdmin(): array
{
    $u = requireAuth();
    if (!$u['es_admin']) {
        json(['error' => 'Sin permiso'], 403);
    }
    return $u;
}

function puedeGestionarCarrera(array $usuario, int $carreraId): bool
{
    if ($usuario['es_admin']) {
        return true;
    }
    $st = db()->prepare('SELECT facultad_id FROM carreras WHERE id = ?');
    $st->execute([$carreraId]);
    $facultad = $st->fetchColumn();
    return $facultad !== false && (int) $facultad === $usuario['facultad_id'];
}
