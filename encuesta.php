<?php
// Encuesta de satisfaccion. Es publica (se llega desde el portal del paciente).
// La cedula se verifica contra la tabla de pacientes y hay un captcha.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/negocio/Captcha.php';
require_once __DIR__ . '/negocio/RespuestaEncuesta.php';
require_once __DIR__ . '/datos/EncuestaDAO.php';

function esc($t) { return htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8'); }

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$dao = new EncuestaDAO();

$errores = [];
$enviada = false;
$encuesta = null;
$encuestas = [];

try {
    if ($id > 0) {
        $encuesta = $dao->obtenerEncuesta($id);
    }
    if ($encuesta === null) {
        $encuestas = $dao->listarEncuestas();
    }
} catch (PDOException $ex) {
    $errores[] = 'No se pudo cargar la encuesta en este momento.';
}

$cedula = '';
$calificacion = 0;
$comentario = '';

if ($encuesta !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuesta = new RespuestaEncuesta($id, $_POST);
    $errores = $respuesta->validar();

    if (!Captcha::verificar($_POST['captcha'] ?? '')) {
        $errores[] = 'La respuesta de verificacion es incorrecta.';
    }

    if (count($errores) === 0) {
        try {
            if (!$dao->existePaciente($respuesta->cedula)) {
                $errores[] = 'La cedula no figura como paciente registrado en el hospital.';
            } else {
                // Solo se puede repetir la encuesta pasado un tiempo (por ejemplo, otra internacion)
                $desde = date('Y-m-d H:i:s', strtotime('-' . DIAS_ENTRE_RESPUESTAS . ' days'));
                if ($dao->respondioDesde($id, $respuesta->cedula, $desde)) {
                    $errores[] = 'Ya respondiste esta encuesta hace menos de ' . DIAS_ENTRE_RESPUESTAS
                               . ' dias. Podes volver a responderla pasado ese plazo.';
                } else {
                    $dao->guardarRespuesta($respuesta);
                    $enviada = true;
                }
            }
        } catch (PDOException $ex) {
            $errores[] = 'No se pudo guardar tu respuesta. Intenta de nuevo.';
        }
    }

    $cedula = $respuesta->cedula;
    $calificacion = $respuesta->calificacion;
    $comentario = $respuesta->comentario;
}

$pregunta = Captcha::nueva();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de satisfacción - <?= esc(SIGLAS_SISTEMA) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="presentacion/css/estilos.css">
</head>
<body>
    <div class="bg-primary text-white py-4">
        <div class="container">
            <h1 class="h3 mb-1">Encuesta de satisfacción</h1>
            <p class="mb-0"><?= esc(SUBTITULO_SISTEMA) ?></p>
        </div>
    </div>

    <div class="container py-4" style="max-width: 700px;">

        <?php if ($enviada): ?>
            <div class="alert alert-success">
                ¡Gracias por responder! Tu opinión nos ayuda a mejorar la atención.
            </div>
            <p><a href="portal.php">Volver al material para pacientes</a></p>

        <?php elseif ($encuesta === null): ?>

            <?php if (count($errores) > 0): ?>
                <div class="alert alert-danger"><?= esc($errores[0]) ?></div>
            <?php endif; ?>

            <h2 class="h5 mb-3">Elegí la encuesta que querés responder</h2>
            <div class="list-group">
                <?php foreach ($encuestas as $e): ?>
                    <a class="list-group-item list-group-item-action" href="encuesta.php?id=<?= (int) $e['id_encuesta'] ?>">
                        <?= esc($e['titulo']) ?>
                    </a>
                <?php endforeach; ?>
                <?php if (count($encuestas) === 0 && count($errores) === 0): ?>
                    <div class="list-group-item text-muted">No hay encuestas disponibles.</div>
                <?php endif; ?>
            </div>
            <p class="mt-4"><a href="portal.php">Volver al material para pacientes</a></p>

        <?php else: ?>

            <h2 class="h5 mb-3"><?= esc($encuesta['titulo']) ?></h2>

            <p class="text-muted small">
                Tu cédula se usa solo para verificar que sos paciente y que no repitas la encuesta.
                En los reportes no se muestra quién respondió.
            </p>

            <?php if (count($errores) > 0): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errores as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body d-block">
                    <form method="post" id="form-encuesta" novalidate>

                        <div class="mb-3">
                            <label class="form-label" for="cedula">Cédula</label>
                            <input type="text" id="cedula" name="cedula" class="form-control" maxlength="8"
                                   inputmode="numeric" value="<?= esc($cedula) ?>">
                            <div class="form-text">Sin puntos ni guión.</div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3" id="grupo-calificacion">
                            <label class="form-label d-block">¿Cómo calificás la atención? (1 = muy mala, 5 = excelente)</label>
                            <?php for ($n = 1; $n <= 5; $n++): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="calificacion"
                                           id="calificacion<?= $n ?>" value="<?= $n ?>"
                                           <?= $calificacion === $n ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="calificacion<?= $n ?>"><?= $n ?></label>
                                </div>
                            <?php endfor; ?>
                            <div class="text-danger small mt-1" id="error-calificacion"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="comentario">Comentario (opcional)</label>
                            <textarea id="comentario" name="comentario" class="form-control" rows="3"
                                      maxlength="500"><?= esc($comentario) ?></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="captcha"><?= esc($pregunta) ?></label>
                            <input type="text" id="captcha" name="captcha" class="form-control" maxlength="3"
                                   inputmode="numeric" autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>

                        <button type="submit" class="btn btn-primary">Enviar respuesta</button>
                        <a href="encuesta.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="presentacion/js/validaciones.js"></script>
</body>
</html>
