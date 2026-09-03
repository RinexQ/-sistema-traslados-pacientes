<?php
// Cierra la sesion activa y vuelve al login.
require_once __DIR__ . '/negocio/Auth.php';

Auth::cerrarSesion();
header('Location: /login.php');
exit;
