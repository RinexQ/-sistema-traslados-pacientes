<?php
// Capa de NEGOCIO
// Un traslado que se asigna a una solicitud aprobada.
// Reglas: no se puede hacer el traslado si el vehiculo esta roto o si no hay chofer.

class Traslado {

    public int $idSolicitud;
    public int $idConductor;
    public ?int $idEnfermero;
    public int $idVehiculo;
    public string $fechaSalida;

    // Recibe el array $_POST del formulario
    public function __construct(int $idSolicitud, array $datos) {
        $this->idSolicitud = $idSolicitud;
        $this->idConductor = (int) ($datos['id_conductor'] ?? 0);
        $enfermero         = (int) ($datos['id_enfermero'] ?? 0);
        $this->idEnfermero = $enfermero > 0 ? $enfermero : null;
        $this->idVehiculo  = (int) ($datos['id_vehiculo'] ?? 0);

        // el campo datetime-local manda "2026-10-20T09:30"
        $fecha = str_replace('T', ' ', trim($datos['fecha_salida'] ?? ''));
        if (strlen($fecha) === 16) {
            $fecha .= ':00';
        }
        $this->fechaSalida = $fecha;
    }

    // $solicitud y $vehiculo son las filas leidas de la base (o null si no existen)
    public function validar(?array $solicitud, ?array $vehiculo): array {
        $errores = [];

        if ($solicitud === null) {
            $errores[] = 'La solicitud no existe.';
        } elseif ($solicitud['estado'] !== 'aprobada') {
            $errores[] = 'Solo se puede asignar un traslado a una solicitud aprobada.';
        } elseif ((int) $solicitud['traslados'] > 0) {
            $errores[] = 'Esta solicitud ya tiene un traslado asignado.';
        }

        if ($this->idConductor <= 0) {
            $errores[] = 'Elegi un conductor: sin chofer no se puede realizar el traslado.';
        }

        if ($vehiculo === null) {
            $errores[] = 'Elegi un vehiculo.';
        } elseif ($vehiculo['estado'] === 'roto') {
            $errores[] = 'El vehiculo elegido esta roto, elegi otro.';
        } elseif ($vehiculo['requiere_enfermero'] && $this->idEnfermero === null) {
            $errores[] = 'Este vehiculo requiere un enfermero en el traslado.';
        }

        $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $this->fechaSalida);
        if ($fecha === false || $fecha->format('Y-m-d H:i:s') !== $this->fechaSalida) {
            $errores[] = 'Indica una fecha y hora de salida valida.';
        }

        return $errores;
    }
}
