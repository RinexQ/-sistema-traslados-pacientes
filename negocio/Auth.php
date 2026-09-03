<?php
// Capa de NEGOCIO
// Funciones para iniciar sesion, verificarla y cerrarla.
// Cualquier pagina protegida llama a Auth::exigirSesion() al principio.

class Auth {

    public static function iniciar(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function guardarUsuario(Usuario $usuario): void {
        self::iniciar();
        $_SESSION['usuario'] = [
            'identificador' => $usuario->identificador,
            'nombre'        => $usuario->nombre,
            'email'         => $usuario->email,
            'rol'           => $usuario->rol,
        ];
    }

    public static function haySesion(): bool {
        self::iniciar();
        return isset($_SESSION['usuario']);
    }

    public static function usuarioActual(): ?array {
        self::iniciar();
        return $_SESSION['usuario'] ?? null;
    }

    public static function esAdministrador(): bool {
        $u = self::usuarioActual();
        return $u !== null && $u['rol'] === 'administrador';
    }

    // Corta la ejecucion y redirige al login si no hay sesion activa.
    public static function exigirSesion(): void {
        self::iniciar();
        if (!self::haySesion()) {
            header('Location: /login.php');
            exit;
        }
    }

    // Ademas de exigir sesion, exige que el rol sea administrador.
    public static function exigirAdministrador(): void {
        self::exigirSesion();
        if (!self::esAdministrador()) {
            http_response_code(403);
            die('No tenes permiso para acceder a esta seccion.');
        }
    }

    public static function cerrarSesion(): void {
        self::iniciar();
        $_SESSION = [];
        session_destroy();
    }
}
