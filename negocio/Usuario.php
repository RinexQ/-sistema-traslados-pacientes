<?php
// Capa de NEGOCIO
// Representa a la persona que inicia sesion y valida sus credenciales.
// Los usuarios se identifican con la cedula.

require_once __DIR__ . '/../datos/UsuarioDAO.php';

class Usuario {

    public string $identificador; // id_admin si es personal, ci si es paciente
    public string $nombre;
    public string $email;
    public string $rol; // 'administrador', 'medico', 'enfermeria' o 'paciente'

    public function __construct(string $identificador, string $nombre, string $email, string $rol) {
        $this->identificador = $identificador;
        $this->nombre        = $nombre;
        $this->email         = $email;
        $this->rol           = $rol;
    }

    // Busca por cedula primero en el personal y despues en pacientes.
    // Devuelve un Usuario si la clave es correcta, o null.
    public static function autenticar(string $cedula, string $passwordPlano): ?Usuario {
        $dao = new UsuarioDAO();

        $admin = $dao->buscarAdministrativoPorCi($cedula);
        if ($admin && password_verify($passwordPlano, $admin['password_hash'])) {
            // el rol viene de la columna "rol" de la tabla administrativo
            $rol = $admin['rol'] ?? 'administrador';
            return new Usuario((string) $admin['id_admin'], $admin['nombre'], $admin['email'], $rol);
        }

        $paciente = $dao->buscarPacientePorCi($cedula);
        if ($paciente && password_verify($passwordPlano, $paciente['password_hash'])) {
            return new Usuario($paciente['ci'], $paciente['nombre'], $paciente['email'], 'paciente');
        }

        return null;
    }
}
