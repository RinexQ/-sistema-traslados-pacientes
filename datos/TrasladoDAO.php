<?php
// Capa de DATOS
// Consultas para asignar traslados y manejar el estado de los vehiculos.

require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/../negocio/Traslado.php';

class TrasladoDAO {

    // Solicitud con la cantidad de traslados que ya tiene asignados
    public function obtenerSolicitud(int $id): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            'SELECT s.id_solicitud, s.tipo, s.prioridad, s.origen, s.destino, s.motivo, s.estado,
                    (SELECT COUNT(*) FROM traslado t WHERE t.id_solicitud = s.id_solicitud) AS traslados
             FROM solicitud_traslado s
             WHERE s.id_solicitud = :id'
        );
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function listarConductores(): array {
        return Conexion::obtener()
            ->query('SELECT id_conductor, nombre FROM conductor ORDER BY nombre')
            ->fetchAll();
    }

    public function listarEnfermeros(): array {
        return Conexion::obtener()
            ->query('SELECT id_enfermero, nombre FROM enfermero ORDER BY nombre')
            ->fetchAll();
    }

    public function listarVehiculos(): array {
        return Conexion::obtener()
            ->query('SELECT id_vehiculo, patente, tipo, requiere_enfermero, estado FROM vehiculo ORDER BY patente')
            ->fetchAll();
    }

    public function obtenerVehiculo(int $id): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM vehiculo WHERE id_vehiculo = :id');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function crear(Traslado $t): int {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            "INSERT INTO traslado (id_solicitud, id_conductor, id_enfermero, id_vehiculo, fecha_salida, hora_llegada, estado)
             VALUES (:solicitud, :conductor, :enfermero, :vehiculo, :salida, NULL, 'programado')"
        );
        $stmt->execute([
            ':solicitud' => $t->idSolicitud,
            ':conductor' => $t->idConductor,
            ':enfermero' => $t->idEnfermero,
            ':vehiculo'  => $t->idVehiculo,
            ':salida'    => $t->fechaSalida,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function cambiarEstadoVehiculo(int $id, string $estado): bool {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('UPDATE vehiculo SET estado = :estado WHERE id_vehiculo = :id');
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
}
