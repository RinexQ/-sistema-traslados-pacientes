<?php
// La confirmacion del usuario ya se pide en el listado (confirm() de JS).
// Este archivo solo ejecuta el DELETE si llega un id valido.
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../datos/DocumentoDAO.php';

Auth::exigirSesion();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($id !== null) {
    $dao = new DocumentoDAO();
    $dao->eliminar($id);
}

header('Location: documentos_listar.php?msg=eliminado');
exit;
