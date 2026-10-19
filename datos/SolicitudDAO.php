<?php
// Capa de DATOS
// Consultas de la tabla solicitud_traslado.

require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/../negocio/Solicitud.php';

class SolicitudDAO {

    // Lista las solicitudes. Si se pasa un id, solo las de ese usuario.
    public function listar(?int $idSolicitante = null): array {
        $pdo = Conexion::obtener();
        $sql = 'SELECT s.id_solicitud, s.fecha, s.tipo, s.origen, s.destino, s.estado,
                       s.domicilio, s.telefono, s.diagnostico, s.motivo,
                       s.aislamiento, s.aislamiento_detalle, s.rodado, s.oxigeno, s.verificado,
                       p.nombre AS paciente, a.nombre AS solicitante
                FROM solicitud_traslado s
                JOIN administrativo a ON a.id_admin = s.id_admin_solicitante
                LEFT JOIN paciente p ON p.ci = s.ci_paciente';
        $params = [];
        if ($idSolicitante !== null) {
            $sql .= ' WHERE s.id_admin_solicitante = :id';
            $params[':id'] = $idSolicitante;
        }
        $sql .= ' ORDER BY s.fecha DESC, s.id_solicitud DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // INSERT: la fecha (hora de la solicitud) la pone la base con NOW()
    public function crear(Solicitud $s, int $idSolicitante): int {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            'INSERT INTO solicitud_traslado
                (fecha, tipo, ci_paciente, origen, destino, domicilio, telefono, diagnostico, motivo,
                 aislamiento, aislamiento_detalle, rodado, oxigeno, verificado, estado, id_admin_solicitante)
             VALUES
                (NOW(), :tipo, :ci, :origen, :destino, :domicilio, :telefono, :diagnostico, :motivo,
                 :aislamiento, :detalle, :rodado, :oxigeno, :verificado, :estado, :solicitante)'
        );
        $stmt->execute([
            ':tipo'        => $s->tipo,
            ':ci'          => $s->ciPaciente,
            ':origen'      => $s->origen,
            ':destino'     => $s->destino,
            ':domicilio'   => $s->domicilio === '' ? null : $s->domicilio,
            ':telefono'    => $s->telefono === '' ? null : $s->telefono,
            ':diagnostico' => $s->diagnostico === '' ? null : $s->diagnostico,
            ':motivo'      => $s->motivo,
            ':aislamiento' => $s->aislamiento ? 1 : 0,
            ':detalle'     => $s->aislamientoDetalle === '' ? null : $s->aislamientoDetalle,
            ':rodado'      => $s->rodado,
            ':oxigeno'     => $s->oxigeno ? 1 : 0,
            ':verificado'  => $s->verificado ? 1 : 0,
            ':estado'      => 'pendiente',
            ':solicitante' => $idSolicitante,
        ]);
        return (int) $pdo->lastInsertId();
    }

    // Solo se puede cambiar el estado si todavia esta pendiente
    public function cambiarEstado(int $id, string $estado): bool {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            "UPDATE solicitud_traslado SET estado = :estado
             WHERE id_solicitud = :id AND estado = 'pendiente'"
        );
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
}
