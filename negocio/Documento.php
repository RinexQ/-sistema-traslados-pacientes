<?php
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
        } elseif (!preg_match('#^(https?://|documentos/)\S+$#i', $this->archivoUrl)) {
            $errores[] = 'La ruta debe comenzar con http://, https:// o documentos/ y no tener espacios.';
        }

        return $errores;
    }

    public function esValido(): bool {
        return count($this->validar()) === 0;
    }
}
