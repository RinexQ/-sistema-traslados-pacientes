<?php
// Descarga de un documento. Es publica (la usa el portal de pacientes).
require_once __DIR__ . '/datos/DocumentoDAO.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$documento = null;

try {
    if ($id > 0) {
        $dao = new DocumentoDAO();
        $documento = $dao->obtenerPorId($id);
    }
} catch (PDOException $ex) {
    $documento = null;
}

if ($documento === null) {
    http_response_code(404);
    echo 'El documento no existe.';
    exit;
}

$ruta = $documento['archivo_url'];

// Si es un enlace externo, se redirige a ese enlace
if (preg_match('#^https?://#i', $ruta)) {
    header('Location: ' . $ruta);
    exit;
}

// Si es un archivo guardado en el proyecto (carpeta documentos/)
$carpeta = realpath(__DIR__ . '/documentos');
$archivo = realpath(__DIR__ . '/' . $ruta);

if ($carpeta === false || $archivo === false || strpos($archivo, $carpeta) !== 0 || !is_file($archivo)) {
    http_response_code(404);
    echo 'El archivo no esta disponible en el servidor.';
    exit;
}

$extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
$tipo = ($extension === 'pdf') ? 'application/pdf' : 'image/jpeg';

header('Content-Type: ' . $tipo);
header('Content-Disposition: attachment; filename="' . basename($archivo) . '"');
header('Content-Length: ' . filesize($archivo));
readfile($archivo);
exit;
