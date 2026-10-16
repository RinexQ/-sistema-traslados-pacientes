<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../datos/PanelDAO.php';

Auth::exigirAdministrador();

$dao = new PanelDAO();
$vehiculos = $dao->listarVehiculos();
$traslados = $dao->listarTraslados();

$coloresEstado = [
    'programado' => 'bg-warning text-dark',
    'en_curso'   => 'bg-primary',
    'finalizado' => 'bg-success',
    'cancelado'  => 'bg-danger',
];

$tituloPagina = 'Ambulancias';
$paginaActiva = 'ambulancias';
require __DIR__ . '/layout_inicio.php';
?>

            <div class="mt-4">
                <h2>Ambulancias</h2>
                <p class="text-muted">Vehículos disponibles y traslados registrados.</p>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Vehículos</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Patente</th><th>Tipo</th><th>Requiere enfermero</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($vehiculos as $v): ?>
                            <tr>
                                <td><?= e($v['patente']) ?></td>
                                <td><?= e($v['tipo']) ?></td>
                                <td><?= $v['requiere_enfermero'] ? 'Sí' : 'No' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Traslados</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Trayecto</th><th>Conductor</th><th>Vehículo</th>
                                <th>Enfermero</th><th>Salida</th><th>Llegada</th><th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($traslados) === 0): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No hay traslados registrados.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($traslados as $t): ?>
                            <tr>
                                <td><?= e($t['origen']) ?> → <?= e($t['destino']) ?></td>
                                <td><?= e($t['conductor']) ?></td>
                                <td><?= e($t['patente']) ?></td>
                                <td><?= e($t['enfermero'] ?? '-') ?></td>
                                <td><?= e(date('d/m/Y H:i', strtotime($t['fecha_salida']))) ?></td>
                                <td><?= e($t['hora_llegada'] ?? '-') ?></td>
                                <td>
                                    <span class="badge <?= e($coloresEstado[$t['estado']] ?? 'bg-secondary') ?>">
                                        <?= e(str_replace('_', ' ', $t['estado'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

<?php require __DIR__ . '/layout_fin.php'; ?>
