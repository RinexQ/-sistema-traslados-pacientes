<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../datos/UsuarioDAO.php';

Auth::exigirAdministrador();

$dao = new UsuarioDAO();
$administrativos = $dao->listarAdministrativos();
$pacientes = $dao->listarPacientes();

$tituloPagina = 'Usuarios';
$paginaActiva = 'usuarios';
require __DIR__ . '/layout_inicio.php';
?>

            <div class="mt-4">
                <h2>Usuarios</h2>
                <p class="text-muted">Personal administrativo y pacientes registrados.</p>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Administrativos</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Cédula</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($administrativos as $a): ?>
                            <tr>
                                <td><?= (int) $a['id_admin'] ?></td>
                                <td><?= e($a['ci']) ?></td>
                                <td><?= e($a['nombre']) ?></td>
                                <td><?= e($a['email']) ?></td>
                                <td><?= e($nombresRol[$a['rol']] ?? $a['rol']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Pacientes</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Cédula</th><th>Nombre</th><th>Email</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pacientes as $p): ?>
                            <tr>
                                <td><?= e($p['ci']) ?></td>
                                <td><?= e($p['nombre']) ?></td>
                                <td><?= e($p['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

<?php require __DIR__ . '/layout_fin.php'; ?>
