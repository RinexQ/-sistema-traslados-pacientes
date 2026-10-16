<?php
require_once __DIR__ . '/Conexion.php';

class UsuarioDAO {

    public function buscarAdministrativoPorEmail(string $email): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM administrativo WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function buscarPacientePorEmail(string $email): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM paciente WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function listarAdministrativos(): array {
        return Conexion::obtener()
            ->query('SELECT id_admin, nombre, email FROM administrativo ORDER BY id_admin')
            ->fetchAll();
    }

    public function listarPacientes(): array {
        return Conexion::obtener()
            ->query('SELECT ci, nombre, email FROM paciente ORDER BY nombre')
            ->fetchAll();
    }
}
