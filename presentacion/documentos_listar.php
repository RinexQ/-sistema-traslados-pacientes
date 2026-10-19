<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../datos/DocumentoDAO.php';

Auth::exigirSesion();

$busqueda = trim($_GET['q'] ?? '');
$mensaje  = $_GET['msg'] ?? '';

// Personal (admin, medico, enfermeria) puede cargar y editar. Solo el admin borra.
$puedeEditar = Auth::esPersonal();
$puedeBorrar = Auth::esAdministrador();

$errorBD = '';
$documentos = [];
try {
    $dao = new DocumentoDAO();
    $documentos = $dao->listarTodos($busqueda);
} catch (PDOException $ex) {
    $errorBD = 'No se pudieron cargar los documentos.';
}

$tituloPagina = 'Documentación';
$paginaActiva = 'documentos';
require __DIR__ . '/layout_inicio.php';
?>

            <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                <h2>Documentación</h2>
            </div>

            <?php if ($mensaje === 'creado'): ?>
                <div class="alert alert-success">Documento creado correctamente.</div>
            <?php elseif ($mensaje === 'editado'): ?>
                <div class="alert alert-success">Documento actualizado correctamente.</div>
            <?php elseif ($mensaje === 'eliminado'): ?>
                <div class="alert alert-success">Documento eliminado correctamente.</div>
            <?php elseif ($mensaje === 'error'): ?>
                <div class="alert alert-danger">No se pudo completar la operación.</div>
            <?php endif; ?>

            <?php if ($errorBD !== ''): ?>
                <div class="alert alert-danger"><?= e($errorBD) ?></div>
            <?php endif; ?>

            <?php if ($busqueda !== ''): ?>
                <div class="alert alert-secondary">
                    Resultados para "<?= e($busqueda) ?>" -
                    <a href="documentos_listar.php">ver todos</a>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Documentos cargados</h5>
                    <?php if ($puedeEditar): ?>
                        <a href="documento_form.php" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-lg"></i> Nuevo documento
                        </a>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Servicio</th>
                                <th>Archivo</th>
                                <th>Fecha de carga</th>
                                <th>Cargado por</th>
                                <th>QR</th>
                                <?php if ($puedeEditar): ?><th>Acciones</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($documentos) === 0): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No hay documentos para mostrar.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($documentos as $doc): ?>
                            <tr>
                                <td><?= e($doc['titulo']) ?></td>
                                <td><?= e($doc['servicio']) ?></td>
                                <td><a href="<?= e($doc['archivo_url']) ?>" target="_blank" rel="noopener">Ver archivo</a></td>
                                <td><?= e($doc['fecha_carga']) ?></td>
                                <td><?= e($doc['admin_nombre']) ?></td>
                                <td>
                                    <?php if (!empty($doc['codigo_qr'])): ?>
                                        <a href="<?= e(Auth::url('ver_documento.php')) ?>?codigo=<?= urlencode($doc['codigo_qr']) ?>"
                                           target="_blank" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-qr-code"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($puedeEditar): ?>
                                <td>
                                    <a href="documento_form.php?id=<?= (int) $doc['id_documento'] ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($puedeBorrar): ?>
                                    <form method="post" action="documento_eliminar.php" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este documento? Esta acción no se puede deshacer.');">
                                        <input type="hidden" name="id" value="<?= (int) $doc['id_documento'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

<?php require __DIR__ . '/layout_fin.php'; ?>
