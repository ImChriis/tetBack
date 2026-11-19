<?php
require_once __DIR__ . '/../../core/http.php';
require_once __DIR__ . '/repo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    metodoNoPermitido();
}
$id = idParam();
$tipo = $_GET['tipo'] ?? '';
if (!in_array($tipo, ['resumen', 'tesis'], true)) {
    json(['error' => 'tipo debe ser "resumen" o "tesis"'], 400);
}

// $tipo ya está en la lista blanca
$st = db()->prepare("SELECT $tipo FROM tet WHERE id = ?");
$st->execute([$id]);
$ruta = rutaPdf($st->fetchColumn() ?: null);
if (!$ruta || !is_file($ruta)) {
    json(['error' => 'Archivo no disponible'], 404);
}

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($ruta));
header("Content-Disposition: inline; filename=\"$tipo-$id.pdf\"");
readfile($ruta);
