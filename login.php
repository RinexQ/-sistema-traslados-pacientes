<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/negocio/Auth.php';
require_once __DIR__ . '/negocio/Usuario.php';
require_once __DIR__ . '/negocio/Captcha.php';

Auth::iniciar();

// Si ya inicio sesion, va directo al panel
if (Auth::haySesion()) {
    header('Location: ' . Auth::url('presentacion/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula   = trim($_POST['cedula'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($cedula === '' || $password === '') {
        $error = 'Completá cédula y contraseña.';
    } elseif (!preg_match('/^\d{7,8}$/', $cedula)) {
        $error = 'La cédula debe tener 7 u 8 dígitos, sin puntos ni guión.';
    } elseif (!Captcha::verificar($_POST['captcha'] ?? '')) {
        $error = 'La respuesta de verificación es incorrecta.';
    } else {
        try {
            $usuario = Usuario::autenticar($cedula, $password);
        } catch (PDOException $ex) {
            $usuario = null;
            $error = 'No se pudo consultar la base de datos.';
        }
        if ($usuario !== null) {
            Auth::guardarUsuario($usuario);
            header('Location: ' . Auth::url('presentacion/index.php'));
            exit;
        }
        if ($error === '') {
            $error = 'Cédula o contraseña incorrectos.';
        }
    }
}

// Pregunta nueva cada vez que se muestra el formulario
$pregunta = Captcha::nueva();

function esc($t) { return htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - <?= esc(SIGLAS_SISTEMA) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="presentacion/css/login_sistema.css">
</head>
<body>
    <div class="login-wrapper">

        <div class="login-left">
            <div class="login-overlay">
                <div class="login-brand">
                    <h1><?= esc(SIGLAS_SISTEMA) ?></h1>
                    <h3><?= esc(SUBTITULO_SISTEMA) ?> - Grupo <?= esc(NOMBRE_SISTEMA) ?></h3>
                    <p>
                        Solicitud de traslados, material de consulta para pacientes,
                        encuestas de satisfacción y reporte de eventos adversos.
                    </p>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-box">
                <div class="login-logo"><i class="bi bi-hospital"></i></div>
                <h2>Iniciar sesión</h2>

                <?php if ($error !== ''): ?>
                    <div class="login-error"><?= esc($error) ?></div>
                <?php endif; ?>

                <form method="post" id="form-login" novalidate>
                    <div class="campo">
                        <label for="cedula">Cédula</label>
                        <input type="text" id="cedula" name="cedula" maxlength="8" inputmode="numeric" required
                               value="<?= esc($_POST['cedula'] ?? '') ?>">
                        <span class="pista">Sin puntos ni guión.</span>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="campo">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="campo">
                        <label for="captcha"><?= esc($pregunta) ?></label>
                        <input type="text" id="captcha" name="captcha" maxlength="3" inputmode="numeric" required autocomplete="off">
                        <div class="invalid-feedback"></div>
                    </div>
                    <button type="submit">Ingresar</button>
                </form>

                <div class="login-link">
                    <a href="portal.php">Ver material de consulta para pacientes</a>
                </div>
            </div>
        </div>

    </div>
    <script src="presentacion/js/validaciones.js"></script>
</body>
</html>
