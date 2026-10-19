<?php
// Capa de NEGOCIO
// Representa una solicitud de traslado (pacientes, material biologico o no biologico).

class Solicitud {

    public string $tipo;
    public ?string $ciPaciente;
    public string $origen;
    public string $destino;
    public string $domicilio;
    public string $telefono;
    public string $diagnostico;
    public string $motivo;
    public bool $aislamiento;
    public string $aislamientoDetalle;
    public string $rodado;
    public bool $oxigeno;
    public bool $verificado;

    // Recibe el array $_POST del formulario
    public function __construct(array $datos) {
        $this->tipo       = $datos['tipo'] ?? 'paciente';
        $ci               = trim($datos['ci_paciente'] ?? '');
        $this->ciPaciente = $ci === '' ? null : $ci;
        $this->origen     = trim($datos['origen'] ?? '');
        $this->destino    = trim($datos['destino'] ?? '');
        $this->domicilio  = trim($datos['domicilio'] ?? '');
        $this->telefono   = trim($datos['telefono'] ?? '');
        $this->diagnostico = trim($datos['diagnostico'] ?? '');
        $this->motivo     = trim($datos['motivo'] ?? '');
        $this->aislamiento = isset($datos['aislamiento']);
        $this->aislamientoDetalle = trim($datos['aislamiento_detalle'] ?? '');
        $this->rodado     = $datos['rodado'] ?? 'ninguno';
        $this->oxigeno    = isset($datos['oxigeno']);
        $this->verificado = isset($datos['verificado']);

        // Si no es un traslado de paciente, estos datos no se usan
        if ($this->tipo !== 'paciente') {
            $this->ciPaciente = null;
            $this->domicilio = '';
            $this->telefono = '';
            $this->diagnostico = '';
            $this->aislamiento = false;
            $this->aislamientoDetalle = '';
            $this->rodado = 'ninguno';
            $this->oxigeno = false;
            $this->verificado = false;
        }
    }

    public function validar(): array {
        $errores = [];

        if (!in_array($this->tipo, ['paciente', 'biologico', 'no_biologico'])) {
            $errores[] = 'El tipo de traslado no es valido.';
        }

        if ($this->origen === '') {
            $errores[] = 'El origen es obligatorio.';
        } elseif (mb_strlen($this->origen) > 200) {
            $errores[] = 'El origen no puede superar los 200 caracteres.';
        }

        if ($this->destino === '') {
            $errores[] = 'El destino es obligatorio.';
        } elseif (mb_strlen($this->destino) > 200) {
            $errores[] = 'El destino no puede superar los 200 caracteres.';
        }

        if ($this->motivo === '') {
            $errores[] = 'El motivo del traslado es obligatorio.';
        } elseif (mb_strlen($this->motivo) > 255) {
            $errores[] = 'El motivo no puede superar los 255 caracteres.';
        }

        if ($this->tipo === 'paciente') {
            if ($this->ciPaciente === null) {
                $errores[] = 'Elegi el paciente a trasladar.';
            }

            if ($this->domicilio === '') {
                $errores[] = 'El domicilio del paciente es obligatorio.';
            } elseif (mb_strlen($this->domicilio) > 200) {
                $errores[] = 'El domicilio no puede superar los 200 caracteres.';
            }

            if (!preg_match('/^[0-9+\s-]{7,20}$/', $this->telefono)) {
                $errores[] = 'El telefono debe tener entre 7 y 20 caracteres (numeros, espacios, + o -).';
            }

            if ($this->diagnostico === '') {
                $errores[] = 'El diagnostico es obligatorio.';
            } elseif (mb_strlen($this->diagnostico) > 255) {
                $errores[] = 'El diagnostico no puede superar los 255 caracteres.';
            }

            if (!in_array($this->rodado, ['ninguno', 'silla', 'camilla'])) {
                $errores[] = 'El rodado elegido no es valido.';
            }

            if ($this->aislamiento && $this->aislamientoDetalle === '') {
                $errores[] = 'Si requiere aislamiento, especifica cual.';
            }

            if (!$this->verificado) {
                $errores[] = 'Tenes que confirmar la lista de verificacion (el paciente requiere traslado).';
            }
        }

        return $errores;
    }
}
