<?php
// Capa de NEGOCIO
// Representa una solicitud de traslado. Los tipos y prioridades salen
// de lo que respondieron los administradores del hospital.

class Solicitud {

    public const TIPOS = [
        'paciente'          => 'Paciente',
        'organo'            => 'Órgano',
        'muestra_biologica' => 'Muestra biológica',
        'cadeteria'         => 'Cadetería de trámites',
        'materiales'        => 'Materiales (insumos y equipamiento)',
    ];

    // Para mostrar los tipos agrupados en el formulario
    public const GRUPOS = [
        'Biológico'    => ['paciente', 'organo', 'muestra_biologica'],
        'No biológico' => ['cadeteria', 'materiales'],
    ];

    public const PRIORIDADES = [
        'sin_prioridad'      => 'Sin prioridad',
        'organos'            => 'Traslado de órganos',
        'dialisis'           => 'Diálisis',
        'tratamiento'        => 'Tratamiento de pacientes',
        'alta_domicilio'     => 'Alta a domicilio',
        'horario_especifico' => 'Horario específico',
    ];

    public string $tipo;
    public string $prioridad;
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
        $this->prioridad  = $datos['prioridad'] ?? 'sin_prioridad';
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

        // Un traslado de organos siempre tiene la prioridad de organos
        if ($this->tipo === 'organo') {
            $this->prioridad = 'organos';
        }

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

        if (!array_key_exists($this->tipo, self::TIPOS)) {
            $errores[] = 'El tipo de traslado no es valido.';
        }

        if (!array_key_exists($this->prioridad, self::PRIORIDADES)) {
            $errores[] = 'La prioridad elegida no es valida.';
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
