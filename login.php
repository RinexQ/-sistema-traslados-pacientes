<?php
// Controlador de login. Si ya hay sesion activa, va directo al panel.
require_once __DIR__ . '/negocio/Auth.php';
require_once __DIR__ . '/negocio/Usuario.php';

Auth::iniciar();
if (Auth::haySesion()) {
    header('Location: /presentacion/panel.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Completa email y contraseña.';
    } else {
        $usuario = Usuario::autenticar($email, $password);
        if ($usuario === null) {
            $error = 'Email o contraseña incorrectos.';
        } else {
            Auth::guardarUsuario($usuario);
            header('Location: /presentacion/panel.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="presentacion/styles.css">
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-sm" style="max-width: 380px; width: 100%;">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">
                    <i class="bi bi-hospital"></i> MediCare
                </h3>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
