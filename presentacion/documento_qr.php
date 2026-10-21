<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../datos/DocumentoDAO.php';

Auth::exigirSesion();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$dao = new DocumentoDAO();
$documento = $id > 0 ? $dao->obtenerPorId($id) : null;
$codigo = $documento !== null ? $dao->obtenerQr($id) : null;

if ($documento === null) {
    header('Location: documentos_listar.php?msg=error');
    exit;
}

// Direccion completa que abre el QR (pagina publica ver_documento.php)
$esquema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$urlQr = '';
if ($codigo !== null) {
    $urlQr = $esquema . '://' . $_SERVER['HTTP_HOST'] . Auth::url('ver_documento.php') . '?codigo=' . urlencode($codigo);
}

$tituloPagina = 'Código QR';
$paginaActiva = 'documentos';
require __DIR__ . '/layout_inicio.php';
?>

            <div class="mt-4">
                <h2>Código QR del documento</h2>
                <p class="text-muted"><?= e($documento['titulo']) ?></p>
            </div>

            <div class="card shadow-sm" style="max-width: 420px;">
                <div class="card-body d-block text-center">
                    <?php if ($codigo === null): ?>
                        <div class="alert alert-warning">Este documento todavía no tiene un código QR.</div>
                    <?php else: ?>
                        <div id="qr" class="d-flex justify-content-center mb-3"></div>
                        <p class="small text-muted text-break"><?= e($urlQr) ?></p>
                        <a id="bajar" href="#" download="qr_documento_<?= (int) $id ?>.png" class="btn btn-primary btn-sm">
                            Descargar QR (PNG)
                        </a>
                        <a href="<?= e($urlQr) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">Probar el enlace</a>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="documentos_listar.php">Volver a documentación</a>
                    </div>
                </div>
            </div>

<?php if ($codigo !== null): ?>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
            <script>
                // Dibuja el QR en la pantalla
                new QRCode(document.getElementById('qr'), {
                    text: <?= json_encode($urlQr) ?>,
                    width: 256,
                    height: 256
                });

                // Al hacer clic en "Descargar" se toma la imagen del QR
                document.getElementById('bajar').addEventListener('click', function () {
                    var lienzo = document.querySelector('#qr canvas');
                    if (lienzo) {
                        this.href = lienzo.toDataURL('image/png');
                    }
                });
            </script>
<?php endif; ?>

<?php require __DIR__ . '/layout_fin.php'; ?>
