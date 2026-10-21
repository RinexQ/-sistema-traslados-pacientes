<?php
// Portal publico para pacientes (se llega por el QR de las pantallas del hospital).
// No pide sesion a proposito.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/datos/DocumentoDAO.php';

function esc($t) { return htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8'); }

$busqueda = trim($_GET['q'] ?? '');
$filas = [];
$error = '';

try {
    $dao = new DocumentoDAO();
    $filas = $dao->listarParaPortal($busqueda);
} catch (PDOException $ex) {
    $error = 'No se pudo cargar el material en este momento.';
}

// Agrupamos los documentos por servicio
$servicios = [];
foreach ($filas as $fila) {
    $nombre = $fila['servicio'];
    if (!isset($servicios[$nombre])) {
        $servicios[$nombre] = [];
    }
    $servicios[$nombre][] = $fila;
}

// Si busco algo, dejamos los servicios abiertos para ver los resultados
$abrir = $busqueda !== '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material para pacientes - <?= esc(SIGLAS_SISTEMA) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="presentacion/css/estilos.css">
</head>
<body>
    <div class="bg-primary text-white py-4">
        <div class="container">
            <h1 class="h3 mb-1">Material de consulta para pacientes</h1>
            <p class="mb-0"><?= esc(SUBTITULO_SISTEMA) ?></p>
        </div>
    </div>

    <div class="container py-4" style="max-width: 800px;">

        <form method="get" class="mb-4">
            <input class="form-control" type="search" name="q"
                   placeholder="Buscar por servicio o por documento..."
                   value="<?= esc($busqueda) ?>">
        </form>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php endif; ?>

        <?php if ($error === '' && count($servicios) === 0): ?>
            <div class="alert alert-secondary">No se encontraron documentos.</div>
        <?php endif; ?>

        <div class="accordion" id="acordeon">
            <?php $n = 0; ?>
            <?php foreach ($servicios as $nombre => $docs): ?>
                <?php $n++; ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $abrir ? '' : 'collapsed' ?>" type="button"
                                data-bs-toggle="collapse" data-bs-target="#servicio<?= $n ?>">
                            <?= esc($nombre) ?>
                            <span class="badge bg-secondary ms-2"><?= count($docs) ?></span>
                        </button>
                    </h2>
                    <div id="servicio<?= $n ?>" class="accordion-collapse collapse <?= $abrir ? 'show' : '' ?>">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <?php foreach ($docs as $d): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?= esc($d['titulo']) ?></span>
                                        <span>
                                            <a href="<?= esc($d['archivo_url']) ?>" target="_blank" rel="noopener"
                                               class="btn btn-primary btn-sm">Abrir</a>
                                            <a href="descargar.php?id=<?= (int) $d['id_documento'] ?>"
                                               class="btn btn-outline-secondary btn-sm">Descargar</a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="alert alert-primary mt-4 mb-0">
            ¿Te atendiste en el hospital? Contanos cómo fue tu experiencia:
            <a href="encuesta.php">responder la encuesta de satisfacción</a>.
        </div>

        <p class="text-center mt-4">
            <a href="login.php">Ingreso para el personal</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
