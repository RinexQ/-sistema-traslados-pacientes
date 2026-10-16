<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/negocio/Auth.php';
require_once __DIR__ . '/negocio/Usuario.php';

Auth::iniciar();
if (Auth::haySesion()) {
    header('Location: ' . Auth::url('presentacion/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Completá email y contraseña.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no tiene un formato válido.';
    } else {
        try {
            $usuario = Usuario::autenticar($email, $password);
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
            $error = 'Email o contraseña incorrectos.';
        }
    }
}

function esc($t) { return htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - <?= esc(NOMBRE_SISTEMA) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="presentacion/styles.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-sm" style="max-width: 380px; width: 100%; height: auto;">
            <div class="card-body d-block p-4">
                <h3 class="text-center mb-4">
                    <i class="bi bi-hospital" style="font-size: 1.75rem;"></i> <?= esc(NOMBRE_SISTEMA) ?>
                </h3>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger py-2"><?= esc($error) ?></div>
                <?php endif; ?>

                <form method="post" id="form-login" novalidate>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required
                               value="<?= esc($_POST['email'] ?? '') ?>">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="presentacion/js/validaciones.js"></script>
</body>
</html>
