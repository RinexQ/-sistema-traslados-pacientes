<?php
// Esta pagina es la que abre el codigo QR. A proposito NO exige sesion:
// un QR lo escanea cualquiera (paciente, familiar) sin haber iniciado sesion
// en el panel administrativo. Solo necesita el codigo del QR por URL.
require_once __DIR__ . '/datos/Conexion.php';

$codigo = $_GET['codigo'] ?? '';
$documento = null;

if ($codigo !== '') {
    $pdo = Conexion::obtener();
    $stmt = $pdo->prepare(
        'SELECT d.titulo, d.archivo_url, d.fecha_carga
         FROM codigo_qr q
         JOIN documento d ON d.id_documento = q.id_documento
         WHERE q.codigo = :codigo'
    );
    $stmt->execute([':codigo' => $codigo]);
    $documento = $stmt->fetch() ?: null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 500px;">
        <?php if ($documento === null): ?>
            <div class="alert alert-warning">
                Código inválido o el documento ya no está disponible.
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4><?= htmlspecialchars($documento['titulo']) ?></h4>
                    <p class="text-muted">Cargado el <?= htmlspecialchars($documento['fecha_carga']) ?></p>
                    <a href="<?= htmlspecialchars($documento['archivo_url']) ?>" target="_blank" class="btn btn-primary">
                        Abrir archivo
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
