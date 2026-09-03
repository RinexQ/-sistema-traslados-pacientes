<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../negocio/Documento.php';
require_once __DIR__ . '/../datos/DocumentoDAO.php';

Auth::exigirSesion();
$usuario = Auth::usuarioActual();
$dao = new DocumentoDAO();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$esEdicion = $id !== null;

$titulo = '';
$archivoUrl = '';
$errores = [];

// Si es edicion, precargamos los datos existentes
if ($esEdicion) {
    $existente = $dao->obtenerPorId($id);
    if ($existente === null) {
        header('Location: documentos_listar.php');
        exit;
    }
    $titulo = $existente['titulo'];
    $archivoUrl = $existente['archivo_url'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $archivoUrl = $_POST['archivo_url'] ?? '';

    // Solo los administradores pueden asignarse a si mismos como admin_carga.
    // Para un paciente logueado no tendria sentido dar de alta un documento, pero dejamos la validacion de rol aca por si se usa mas adelante.
    $idAdminCarga = $usuario['rol'] === 'administrador' ? (int) $usuario['identificador'] : null;

    $documento = new Documento($id, $titulo, $archivoUrl);
    $errores = $documento->validar();

    if (count($errores) === 0) {
        if ($esEdicion) {
            $dao->actualizar($id, $documento->titulo, $documento->archivoUrl);
            header('Location: documentos_listar.php?msg=editado');
        } else {
            if ($idAdminCarga === null) {
                $errores[] = 'Solo un administrador puede cargar documentos.';
            } else {
                $dao->crear($documento->titulo, $documento->archivoUrl, $idAdminCarga);
                header('Location: documentos_listar.php?msg=creado');
            }
        }
        if (count($errores) === 0) {
            exit;
        }
    }

    // si hubo errores, mantenemos lo que el usuario escribio
    $titulo = $documento->titulo;
    $archivoUrl = $documento->archivoUrl;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $esEdicion ? 'Editar documento' : 'Nuevo documento' ?></title>
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
            <h2><?= $esEdicion ? 'Editar documento' : 'Nuevo documento' ?></h2>

            <?php if (count($errores) > 0): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errores as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm" style="max-width: 600px;">
                <div class="card-body">
                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" maxlength="200"
                                   value="<?= htmlspecialchars($titulo) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL / ruta del archivo</label>
                            <input type="text" name="archivo_url" class="form-control" maxlength="500"
                                   value="<?= htmlspecialchars($archivoUrl) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <?= $esEdicion ? 'Guardar cambios' : 'Crear documento' ?>
                        </button>
                        <a href="documentos_listar.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
