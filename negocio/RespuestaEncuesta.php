<?php
// Capa de NEGOCIO
// Una respuesta a una encuesta de satisfaccion.

class RespuestaEncuesta {

    public int $idEncuesta;
    public string $cedula;
    public int $calificacion;
    public string $comentario;

    // Recibe el array $_POST del formulario
    public function __construct(int $idEncuesta, array $datos) {
        $this->idEncuesta   = $idEncuesta;
        $this->cedula       = trim($datos['cedula'] ?? '');
        $this->calificacion = (int) ($datos['calificacion'] ?? 0);
        $this->comentario   = trim($datos['comentario'] ?? '');
    }

    public function validar(): array {
        $errores = [];

        if (!preg_match('/^\d{7,8}$/', $this->cedula)) {
            $errores[] = 'La cedula debe tener 7 u 8 digitos, sin puntos ni guion.';
        }

        if ($this->calificacion < 1 || $this->calificacion > 5) {
            $errores[] = 'Elegi una calificacion del 1 al 5.';
        }

        if (mb_strlen($this->comentario) > 500) {
            $errores[] = 'El comentario no puede superar los 500 caracteres.';
        }

        return $errores;
    }
}
