<?php
// Capa de DATOS
// Consultas de usuarios. Las busquedas de login son por cedula.

require_once __DIR__ . '/Conexion.php';

class UsuarioDAO {

    public function buscarAdministrativoPorCi(string $ci): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM administrativo WHERE ci = :ci');
        $stmt->execute([':ci' => $ci]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function buscarPacientePorCi(string $ci): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM paciente WHERE ci = :ci');
        $stmt->execute([':ci' => $ci]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function listarAdministrativos(): array {
        return Conexion::obtener()
            ->query('SELECT id_admin, ci, nombre, email, rol FROM administrativo ORDER BY id_admin')
            ->fetchAll();
    }

    public function listarPacientes(): array {
        return Conexion::obtener()
            ->query('SELECT ci, nombre, email FROM paciente ORDER BY nombre')
            ->fetchAll();
    }
}
