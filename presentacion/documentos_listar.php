<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../datos/DocumentoDAO.php';

Auth::exigirSesion();

$dao = new DocumentoDAO();
$documentos = $dao->listarTodos();

$mensaje = $_GET['msg'] ?? '';
$usuario = Auth::usuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 bg-primary text-white min-vh-100 p-3 d-none d-lg-block">
            <h3 class="text-center mb-4"><i class="bi bi-hospital"></i> MediCare</h3>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link text-white" href="panel.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link text-white active" href="documentos_listar.php"><i class="bi bi-file-earmark-text"></i> Documentación</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
            </ul>
        </aside>

        <main class="col-12 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Documentación</h2>
                <span class="text-muted">
                    Sesión: <?= htmlspecialchars($usuario['nombre']) ?> (<?= htmlspecialchars($usuario['rol']) ?>)
                </span>
            </div>

            <?php if ($mensaje === 'creado'): ?>
                <div class="alert alert-success">Documento creado correctamente.</div>
            <?php elseif ($mensaje === 'editado'): ?>
                <div class="alert alert-success">Documento actualizado correctamente.</div>
            <?php elseif ($mensaje === 'eliminado'): ?>
                <div class="alert alert-success">Documento eliminado correctamente.</div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Documentos cargados</h5>
                    <a href="documento_form.php" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-lg"></i> Nuevo documento
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Archivo</th>
                                <th>Fecha de carga</th>
                                <th>Cargado por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($documentos) === 0): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">No hay documentos cargados todavía.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($documentos as $doc): ?>
                            <tr>
                                <td><?= htmlspecialchars($doc['titulo']) ?></td>
                                <td><a href="<?= htmlspecialchars($doc['archivo_url']) ?>" target="_blank">Ver archivo</a></td>
                                <td><?= htmlspecialchars($doc['fecha_carga']) ?></td>
                                <td><?= htmlspecialchars($doc['admin_nombre']) ?></td>
                                <td>
                                    <a href="documento_form.php?id=<?= (int)$doc['id_documento'] ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="documento_eliminar.php?id=<?= (int)$doc['id_documento'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('¿Eliminar este documento? Esta acción no se puede deshacer.');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
