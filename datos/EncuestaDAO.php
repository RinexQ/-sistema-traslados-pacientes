<?php
// Capa de DATOS
// Consultas de encuestas y respuestas.

require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/../negocio/RespuestaEncuesta.php';

class EncuestaDAO {

    public function listarEncuestas(): array {
        return Conexion::obtener()
            ->query('SELECT id_encuesta, titulo, es_anonima FROM encuesta ORDER BY titulo')
            ->fetchAll();
    }

    public function obtenerEncuesta(int $id): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT id_encuesta, titulo, es_anonima FROM encuesta WHERE id_encuesta = :id');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    // Verifica la cedula contra la tabla de pacientes
    public function existePaciente(string $ci): bool {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM paciente WHERE ci = :ci');
        $stmt->execute([':ci' => $ci]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // true si esa cedula ya respondio esa encuesta desde la fecha $desde
    public function respondioDesde(int $idEncuesta, string $ci, string $desde): bool {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM respuesta
             WHERE id_encuesta = :encuesta AND ci_paciente = :ci AND fecha >= :desde'
        );
        $stmt->execute([':encuesta' => $idEncuesta, ':ci' => $ci, ':desde' => $desde]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function guardarRespuesta(RespuestaEncuesta $r): int {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            'INSERT INTO respuesta (id_encuesta, ci_paciente, respuesta_texto, calificacion, fecha)
             VALUES (:encuesta, :ci, :texto, :calificacion, NOW())'
        );
        $stmt->execute([
            ':encuesta'     => $r->idEncuesta,
            ':ci'           => $r->cedula,
            ':texto'        => $r->comentario,
            ':calificacion' => $r->calificacion,
        ]);
        return (int) $pdo->lastInsertId();
    }

    // Cantidad de respuestas y promedio por encuesta (no muestra quien respondio)
    public function reporte(): array {
        $sql = 'SELECT e.id_encuesta, e.titulo, e.es_anonima,
                       COUNT(r.id_respuesta) AS total, AVG(r.calificacion) AS promedio
                FROM encuesta e
                LEFT JOIN respuesta r ON r.id_encuesta = e.id_encuesta
                GROUP BY e.id_encuesta, e.titulo, e.es_anonima
                ORDER BY e.titulo';
        return Conexion::obtener()->query($sql)->fetchAll();
    }

    public function comentariosRecientes(int $idEncuesta, int $limite = 5): array {
        $limite = (int) $limite;
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            "SELECT respuesta_texto, calificacion, fecha FROM respuesta
             WHERE id_encuesta = :id AND respuesta_texto <> ''
             ORDER BY fecha DESC LIMIT $limite"
        );
        $stmt->execute([':id' => $idEncuesta]);
        return $stmt->fetchAll();
    }
}
