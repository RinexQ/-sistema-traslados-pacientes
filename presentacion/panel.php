<?php
// Esta es la RUTA PROTEGIDA real del dashboard.
// index.html no se modifica: este archivo solo lo sirve si hay sesion activa.
require_once __DIR__ . '/../negocio/Auth.php';

Auth::exigirSesion(); // si no hay sesion, redirige a /login.php y corta la ejecucion

readfile(__DIR__ . '/index.html');
