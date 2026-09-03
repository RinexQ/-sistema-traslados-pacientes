<?php
// Capa de DATOS
// Consultas SQL relacionadas al login. No contiene logica de negocio,
// solo lee/escribe filas y las devuelve como arrays.

require_once __DIR__ . '/Conexion.php';

class UsuarioDAO {

    // Busca un administrativo por email. Devuelve el array de la fila o null.
    public function buscarAdministrativoPorEmail(string $email): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM administrativo WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    // Busca un paciente por email. Devuelve el array de la fila o null.
    public function buscarPacientePorEmail(string $email): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM paciente WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }
}
