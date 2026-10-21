<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../datos/EncuestaDAO.php';

// Reporte de satisfaccion para administrador, medico y enfermeria
Auth::exigirPersonal();

$errorBD = '';
$resumen = [];
$comentarios = [];

try {
    $dao = new EncuestaDAO();
    $resumen = $dao->reporte();
    foreach ($resumen as $fila) {
        $comentarios[$fila['id_encuesta']] = $dao->comentariosRecientes((int) $fila['id_encuesta'], 5);
    }
} catch (PDOException $ex) {
    $errorBD = 'No se pudo cargar el reporte.';
}

$tituloPagina = 'Encuestas';
$paginaActiva = 'encuestas';
require __DIR__ . '/layout_inicio.php';
?>

            <div class="mt-4 mb-3">
                <h2>Encuestas de satisfacción</h2>
                <p class="text-muted">
                    Los pacientes responden desde el portal (<a href="../encuesta.php" target="_blank">ver encuestas</a>).
                    El reporte no muestra quién respondió.
                </p>
            </div>

            <?php if ($errorBD !== ''): ?>
                <div class="alert alert-danger"><?= e($errorBD) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Resultados por encuesta</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Encuesta</th>
                                <th>Respuestas</th>
                                <th>Promedio (1 a 5)</th>
                                <th>Últimos comentarios</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($resumen) === 0): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No hay encuestas cargadas.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($resumen as $fila): ?>
                            <tr>
                                <td><?= e($fila['titulo']) ?></td>
                                <td><?= (int) $fila['total'] ?></td>
                                <td><?= $fila['promedio'] === null ? '-' : e(number_format((float) $fila['promedio'], 1)) ?></td>
                                <td>
                                    <?php $lista = $comentarios[$fila['id_encuesta']] ?? []; ?>
                                    <?php if (count($lista) === 0): ?>
                                        <span class="text-muted">Sin comentarios</span>
                                    <?php else: ?>
                                        <details>
                                            <summary>Ver (<?= count($lista) ?>)</summary>
                                            <ul class="small mt-2 mb-0">
                                                <?php foreach ($lista as $c): ?>
                                                    <li>
                                                        <?= e($c['respuesta_texto']) ?>
                                                        <span class="text-muted">(<?= e($c['calificacion'] ?? '-') ?>/5)</span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </details>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

<?php require __DIR__ . '/layout_fin.php'; ?>
