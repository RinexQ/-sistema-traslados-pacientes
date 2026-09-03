<?php
// Capa de NEGOCIO
// Representa un documento del sistema y valida sus datos antes de que la capa de Datos los guarde.

class Documento {

    public ?int $id;
    public string $titulo;
    public string $archivoUrl;
    public ?string $fechaCarga;
    public ?int $idAdminCarga;

    public function __construct(?int $id, string $titulo, string $archivoUrl, ?string $fechaCarga = null, ?int $idAdminCarga = null) {
        $this->id           = $id;
        $this->titulo       = trim($titulo);
        $this->archivoUrl   = trim($archivoUrl);
        $this->fechaCarga   = $fechaCarga;
        $this->idAdminCarga = $idAdminCarga;
    }

    // Valida los datos del documento. Devuelve un array de errores
    // Si todo está bien no debería mostrar nada. Esto es lo que muestra el formulario.
    public function validar(): array {
        $errores = [];

        if ($this->titulo === '') {
            $errores[] = 'El titulo es obligatorio.';
        } elseif (mb_strlen($this->titulo) > 200) {
            $errores[] = 'El titulo no puede superar los 200 caracteres.';
        }

        if ($this->archivoUrl === '') {
            $errores[] = 'Debes indicar la URL o ruta del archivo.';
        } elseif (mb_strlen($this->archivoUrl) > 500) {
            $errores[] = 'La URL del archivo es demasiado larga.';
        }

        return $errores;
    }

    public function esValido(): bool {
        return count($this->validar()) === 0;
    }
}
