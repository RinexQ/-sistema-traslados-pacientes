<?php
require_once __DIR__ . '/negocio/Auth.php';

header('Location: ' . Auth::url(Auth::haySesion() ? 'presentacion/index.php' : 'login.php'));
exit;
