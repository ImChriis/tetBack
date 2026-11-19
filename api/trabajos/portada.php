<?php
require_once __DIR__ . '/../../core/http.php';
require_once __DIR__ . '/repo.php';

// Primera página del trabajo completo (la portada) como PNG.
// Requiere pdftoppm (paquete poppler-utils) en el servidor.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    metodoNoPermitido();
}
$id = idParam();

$st = db()->prepare('SELECT tesis FROM tet WHERE id = ?');
$st->execute([$id]);
$pdf = rutaPdf($st->fetchColumn() ?: null);
if (!$pdf || !is_file($pdf)) {
    json(['error' => 'Este trabajo no tiene el PDF completo'], 404);
}

$png = rutaPortada($pdf);
if (!is_file($png)) {
    if (!is_dir(dirname($png))) {
        mkdir(dirname($png), 0775, true);
    }
    // pdftoppm agrega ".png" al nombre de salida.
    // XAMPP exporta LD_LIBRARY_PATH con sus librerías viejas, que rompen pdftoppm del sistema: se quita.
    $salida = substr($png, 0, -4);
    exec('env -u LD_LIBRARY_PATH pdftoppm -f 1 -l 1 -singlefile -png -scale-to 1000 ' . escapeshellarg($pdf) . ' ' . escapeshellarg($salida) . ' 2>&1', $log, $codigo);
    if ($codigo !== 0 || !is_file($png)) {
        error_log("portada $id: pdftoppm falló ($codigo): " . implode(' ', $log));
        json(['error' => 'No se pudo generar la portada'], 500);
    }
}

header('Content-Type: image/png');
header('Content-Length: ' . filesize($png));
header('Cache-Control: public, max-age=86400');
readfile($png);
