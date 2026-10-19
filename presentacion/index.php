<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../datos/PanelDAO.php';

Auth::exigirSesion();
if (!Auth::esAdministrador()) {
    header('Location: documentos_listar.php');
    exit;
}

$dao = new PanelDAO();
$totalAmbulancias = $dao->contarVehiculos();
$totalDocumentos  = $dao->contarDocumentos();
$totalActivos     = $dao->contarTrasladosActivos();
$traslados        = $dao->trasladosRecientes(5);
$actividad        = $dao->actividadReciente(5);

$coloresEstado = [
    'programado' => 'bg-warning text-dark',
    'en_curso'   => 'bg-primary',
    'finalizado' => 'bg-success',
    'cancelado'  => 'bg-danger',
];
$iconosActividad = [
    'documento' => 'bi-file-earmark-check text-warning',
    'traslado'  => 'bi-truck text-primary',
    'respuesta' => 'bi-chat-dots text-success',
];

$tituloPagina = 'Dashboard';
$paginaActiva = 'index';
require __DIR__ . '/layout_inicio.php';
?>

            <div class="mt-4">
                <h2>Dashboard</h2>
                <p class="text-muted">Bienvenido al panel administrativo.</p>
                <div class="alert alert-primary rounded-4">
                    <i class="bi bi-info-circle-fill"></i>
                    Desde este panel podrás gestionar ambulancias, documentación y usuarios.
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6>Ambulancias</h6>
                                    <h2><?= $totalAmbulancias ?></h2>
                                </div>
                                <i class="bi bi-truck text-success fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6>Documentos</h6>
                                    <h2><?= $totalDocumentos ?></h2>
                                </div>
                                <i class="bi bi-file-earmark-text text-warning fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6>Traslados activos</h6>
                                    <h2><?= $totalActivos ?></h2>
                                </div>
                                <i class="bi bi-calendar-check text-danger fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">

                <div class="col-12 col-xl-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Traslados recientes</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Trayecto</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (count($traslados) === 0): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">No hay traslados registrados.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($traslados as $t): ?>
                                    <tr>
                                        <td><?= e($t['origen']) ?> → <?= e($t['destino']) ?></td>
                                        <td>
                                            <span class="badge <?= e($coloresEstado[$t['estado']] ?? 'bg-secondary') ?>">
                                                <?= e(ucfirst(str_replace('_', ' ', $t['estado']))) ?>
                                            </span>
                                        </td>
                                        <td><?= e(date('d/m/Y H:i', strtotime($t['fecha_salida']))) ?></td>
                                        <td>
                                            <a href="ambulancias.php" class="btn btn-primary btn-sm">Ver</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Actividad reciente</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php foreach ($actividad as $a): ?>
                                <div class="list-group-item">
                                    <i class="bi <?= e($iconosActividad[$a['tipo']] ?? 'bi-circle') ?> me-2"></i>
                                    <?= e($a['texto']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Accesos rápidos</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2 w-100">
                                <a href="ambulancias.php" class="btn btn-primary">Ver ambulancias</a>
                                <a href="documento_form.php" class="btn btn-success">Nuevo documento</a>
                                <a href="usuarios.php" class="btn btn-warning text-dark">Ver usuarios</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

<?php require __DIR__ . '/layout_fin.php'; ?>
