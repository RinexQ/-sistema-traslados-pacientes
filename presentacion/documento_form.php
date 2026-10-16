<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../negocio/Documento.php';
require_once __DIR__ . '/../datos/DocumentoDAO.php';

Auth::exigirAdministrador();
$usuario = Auth::usuarioActual();
$dao = new DocumentoDAO();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$esEdicion = $id !== null;

$titulo = '';
$archivoUrl = '';
$errores = [];

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

    $documento = new Documento($id, $titulo, $archivoUrl);
    $errores = $documento->validar();

    if (count($errores) === 0) {
        try {
            if ($esEdicion) {
                $dao->actualizar($id, $documento->titulo, $documento->archivoUrl);
                header('Location: documentos_listar.php?msg=editado');
            } else {
                $dao->crear($documento->titulo, $documento->archivoUrl, (int) $usuario['identificador']);
                header('Location: documentos_listar.php?msg=creado');
            }
            exit;
        } catch (PDOException $ex) {
            $errores[] = 'No se pudo guardar el documento. Intentá de nuevo.';
        }
    }

    $titulo = $documento->titulo;
    $archivoUrl = $documento->archivoUrl;
}

$tituloPagina = $esEdicion ? 'Editar documento' : 'Nuevo documento';
$paginaActiva = 'documentos';
$scriptsExtra = ['js/validaciones.js'];
require __DIR__ . '/layout_inicio.php';
?>

            <div class="mt-4">
                <h2><?= $esEdicion ? 'Editar documento' : 'Nuevo documento' ?></h2>
            </div>

            <?php if (count($errores) > 0): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errores as $err): ?>
                            <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm" style="max-width: 600px;">
                <div class="card-body d-block">
                    <form method="post" id="form-documento" novalidate>
                        <div class="mb-3">
                            <label class="form-label" for="titulo">Título</label>
                            <input type="text" id="titulo" name="titulo" class="form-control" maxlength="200"
                                   value="<?= e($titulo) ?>" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="archivo_url">URL / ruta del archivo</label>
                            <input type="text" id="archivo_url" name="archivo_url" class="form-control" maxlength="500"
                                   value="<?= e($archivoUrl) ?>" required>
                            <div class="form-text">Ejemplo: https://ejemplo.com/doc.pdf o documentos/indicacion_001.pdf</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <?= $esEdicion ? 'Guardar cambios' : 'Crear documento' ?>
                        </button>
                        <a href="documentos_listar.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>

<?php require __DIR__ . '/layout_fin.php'; ?>
