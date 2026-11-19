<?php
require_once __DIR__ . '/../config/config.php';

header('Access-Control-Allow-Origin: ' . CONFIG['cors_origin']);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Expose-Headers: Content-Disposition');
header('Vary: Origin');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Ningún error interno llega al cliente con detalles
set_exception_handler(function (Throwable $e) {
    error_log((string) $e);
    json(['error' => 'Error interno del servidor'], 500);
});

function json(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function body(): array
{
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}

function requireFields(array $data, array $campos): void
{
    $faltan = array_values(array_filter($campos, fn($c) => trim((string) ($data[$c] ?? '')) === ''));
    if ($faltan) {
        json(['error' => 'Faltan campos obligatorios', 'campos' => $faltan], 400);
    }
}

function idParam(): int
{
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id) {
        json(['error' => 'id inválido'], 400);
    }
    return $id;
}

function metodoNoPermitido(): never
{
    json(['error' => 'Método no permitido'], 405);
}
