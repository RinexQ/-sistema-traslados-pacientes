<?php
// Capa de NEGOCIO
// Representa un documento y valida sus datos antes de guardarlos.

class Documento {

    public ?int $id;
    public string $titulo;
    public string $archivoUrl;
    public ?string $fechaCarga;
    public ?int $idAdminCarga;
    public ?int $idServicio;

    public function __construct(?int $id, string $titulo, string $archivoUrl, ?string $fechaCarga = null, ?int $idAdminCarga = null, ?int $idServicio = null) {
        $this->id           = $id;
        $this->titulo       = trim($titulo);
        $this->archivoUrl   = trim($archivoUrl);
        $this->fechaCarga   = $fechaCarga;
        $this->idAdminCarga = $idAdminCarga;
        $this->idServicio   = $idServicio;
    }

    // Devuelve una lista de errores (vacia si esta todo bien)
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
        } elseif (strpos($this->archivoUrl, '..') !== false) {
            $errores[] = 'La ruta del archivo no es valida.';
        } elseif (!preg_match('#\.(pdf|jpe?g)$#i', $this->archivoUrl)) {
            $errores[] = 'El archivo tiene que ser un PDF o una imagen JPG.';
        }

        if ($this->idServicio === null || $this->idServicio <= 0) {
            $errores[] = 'Elegi el servicio al que pertenece el documento.';
        }

        return $errores;
    }

    public function esValido(): bool {
        return count($this->validar()) === 0;
    }
}
