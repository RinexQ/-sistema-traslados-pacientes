<?php
require_once __DIR__ . '/../negocio/Auth.php';
require_once __DIR__ . '/../datos/DocumentoDAO.php';

Auth::exigirPersonal();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: documentos_listar.php');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$resultado = 'error';

if ($id > 0) {
    try {
        $dao = new DocumentoDAO();
        $dao->eliminar($id);
        $resultado = 'eliminado';
    } catch (PDOException $ex) {
        $resultado = 'error';
    }
}

header('Location: documentos_listar.php?msg=' . $resultado);
exit;
