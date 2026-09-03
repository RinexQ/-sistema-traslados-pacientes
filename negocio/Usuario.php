<?php
// Capa de NEGOCIO
// Representa a la persona que inicia sesion (administrativo o paciente y contiene la logica para validar sus credenciales.

require_once __DIR__ . '/../datos/UsuarioDAO.php';

class Usuario {

    public string $identificador; // id_admin si es administrador, ci si es paciente
    public string $nombre;
    public string $email;
    public string $rol; // 'administrador' o 'paciente'

    public function __construct(string $identificador, string $nombre, string $email, string $rol) {
        $this->identificador = $identificador;
        $this->nombre         = $nombre;
        $this->email          = $email;
        $this->rol            = $rol;
    }

    // Intenta autenticar contra administrativo y despues contra paciente.
    // Devuelve un objeto Usuario si las credenciales son correctas, o null.
    public static function autenticar(string $email, string $passwordPlano): ?Usuario {
        $dao = new UsuarioDAO();

        $admin = $dao->buscarAdministrativoPorEmail($email);
        if ($admin && password_verify($passwordPlano, $admin['password_hash'])) {
            return new Usuario((string)$admin['id_admin'], $admin['nombre'], $admin['email'], 'administrador');
        }

        $paciente = $dao->buscarPacientePorEmail($email);
        if ($paciente && password_verify($passwordPlano, $paciente['password_hash'])) {
            return new Usuario($paciente['ci'], $paciente['nombre'], $paciente['email'], 'paciente');
        }

        return null;
    }
}
