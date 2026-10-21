<?php
// Capa de NEGOCIO
// Captcha simple: una suma que se guarda en la sesion.

require_once __DIR__ . '/Auth.php';

class Captcha {

    // Genera una suma nueva y devuelve la pregunta para mostrar
    public static function nueva(): string {
        Auth::iniciar();
        $a = rand(1, 9);
        $b = rand(1, 9);
        $_SESSION['captcha_resultado'] = $a + $b;
        return '¿Cuánto es ' . $a . ' + ' . $b . '?';
    }

    // Compara la respuesta. Cada captcha se puede usar una sola vez.
    public static function verificar($respuesta): bool {
        Auth::iniciar();
        $esperado = $_SESSION['captcha_resultado'] ?? null;
        unset($_SESSION['captcha_resultado']);

        $respuesta = trim((string) $respuesta);
        if ($esperado === null || !ctype_digit($respuesta)) {
            return false;
        }
        return (int) $respuesta === (int) $esperado;
    }
}
