<?php
class Auth {

    public static function iniciar(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function base(): string {
        $raiz = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));
        $doc  = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
        if ($doc !== '' && stripos($raiz, $doc) === 0) {
            return rtrim(substr($raiz, strlen($doc)), '/');
        }
        return '';
    }

    public static function url(string $ruta): string {
        return self::base() . '/' . ltrim($ruta, '/');
    }

    public static function guardarUsuario(Usuario $usuario): void {
        self::iniciar();
        session_regenerate_id(true);
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

    public static function exigirSesion(): void {
        self::iniciar();

        // Cerrar sesion: cualquier pagina protegida con ?salir=1
        if (isset($_GET['salir'])) {
            self::cerrarSesion();
            header('Location: ' . self::url('login.php'));
            exit;
        }

        if (!self::haySesion()) {
            header('Location: ' . self::url('login.php'));
            exit;
        }
    }

    // Personal del hospital: administrador, medico o enfermeria (todos menos paciente)
    public static function esPersonal(): bool {
        $u = self::usuarioActual();
        return $u !== null && $u['rol'] !== 'paciente';
    }

    public static function exigirPersonal(): void {
        self::exigirSesion();
        if (!self::esPersonal()) {
            header('Location: ' . self::url('presentacion/documentos_listar.php'));
            exit;
        }
    }

    public static function exigirAdministrador(): void {
        self::exigirSesion();
        if (!self::esAdministrador()) {
            header('Location: ' . self::url('presentacion/documentos_listar.php'));
            exit;
        }
    }

    public static function cerrarSesion(): void {
        self::iniciar();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}
