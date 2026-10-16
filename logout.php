<?php
require_once __DIR__ . '/negocio/Auth.php';

Auth::cerrarSesion();
header('Location: ' . Auth::url('login.php'));
exit;
