<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../negocio/Auth.php';

if (!function_exists('e')) {
    function e($texto): string {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }
}

$usuarioLayout = Auth::usuarioActual();
$esAdminLayout = Auth::esAdministrador();

$menu = [];
if ($esAdminLayout) {
    $menu[] = ['index', 'index.php', 'bi-speedometer2', 'Dashboard'];
    $menu[] = ['ambulancias', 'ambulancias.php', 'bi-truck', 'Ambulancias'];
}
$menu[] = ['documentos', 'documentos_listar.php', 'bi-file-earmark-text', 'Documentación'];
if ($esAdminLayout) {
    $menu[] = ['usuarios', 'usuarios.php', 'bi-person-badge', 'Usuarios'];
}
$menu[] = ['salir', '../logout.php', 'bi-box-arrow-right', 'Cerrar sesión'];

$busquedaActual = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tituloPagina) ?> - <?= e(NOMBRE_SISTEMA) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <aside class="col-lg-2 bg-primary text-white min-vh-100 p-3 d-none d-lg-block">
            <h3 class="text-center mb-4">
                <i class="bi bi-hospital"></i>
                <?= e(NOMBRE_SISTEMA) ?>
            </h3>
            <hr>
            <ul class="nav flex-column">
                <?php foreach ($menu as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white <?= $item[0] === $paginaActiva ? 'active' : '' ?>" href="<?= e($item[1]) ?>">
                            <i class="bi <?= e($item[2]) ?>"></i>
                            <?= e($item[3]) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main class="col-12 col-lg-10 p-4">

            <div class="d-lg-none mb-3">
                <div class="row g-2">
                    <?php foreach ($menu as $item): ?>
                        <div class="col-6">
                            <a class="btn btn-primary w-100" href="<?= e($item[1]) ?>"><?= e($item[3]) ?></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <nav class="navbar bg-white rounded shadow-sm px-4 py-3">
                <div class="container-fluid">
                    <div class="row w-100 align-items-center">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <form method="get" action="documentos_listar.php">
                                <input class="form-control" type="search" name="q"
                                       placeholder="Buscar documentos..."
                                       value="<?= e($busquedaActual) ?>">
                            </form>
                        </div>
                        <div class="col-12 col-md-6 text-center text-md-end">
                            <span class="me-3 d-none d-md-inline text-muted">
                                <?= e($usuarioLayout['nombre'] ?? '') ?>
                            </span>
                            <i class="bi bi-person-circle fs-2"></i>
                        </div>
                    </div>
                </div>
            </nav>
