<?php
// Capa de DATOS
// CRUD de la tabla documento (siempre con consultas preparadas).

require_once __DIR__ . '/Conexion.php';

class DocumentoDAO {

    // Lista para el panel. Si hay busqueda, filtra por titulo o por servicio.
    public function listarTodos(?string $busqueda = null): array {
        $pdo = Conexion::obtener();
        $sql = 'SELECT d.id_documento, d.titulo, d.archivo_url, d.fecha_carga,
                       a.nombre AS admin_nombre, s.nombre AS servicio,
                       (SELECT q.codigo FROM codigo_qr q
                         WHERE q.id_documento = d.id_documento
                         ORDER BY q.id_qr LIMIT 1) AS codigo_qr
                FROM documento d
                JOIN administrativo a ON a.id_admin = d.id_admin_carga
                JOIN servicio s ON s.id_servicio = d.id_servicio';
        $params = [];
        if ($busqueda !== null && $busqueda !== '') {
            $sql .= ' WHERE d.titulo LIKE :q1 OR s.nombre LIKE :q2';
            $params[':q1'] = '%' . $busqueda . '%';
            $params[':q2'] = '%' . $busqueda . '%';
        }
        $sql .= ' ORDER BY d.fecha_carga DESC, d.id_documento DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lista para el portal publico, ordenada por servicio
    public function listarParaPortal(?string $busqueda = null): array {
        $pdo = Conexion::obtener();
        $sql = 'SELECT d.id_documento, s.nombre AS servicio, d.titulo, d.archivo_url, d.fecha_carga
                FROM documento d
                JOIN servicio s ON s.id_servicio = d.id_servicio';
        $params = [];
        if ($busqueda !== null && $busqueda !== '') {
            $sql .= ' WHERE d.titulo LIKE :q1 OR s.nombre LIKE :q2';
            $params[':q1'] = '%' . $busqueda . '%';
            $params[':q2'] = '%' . $busqueda . '%';
        }
        $sql .= ' ORDER BY s.nombre, d.titulo';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listarServicios(): array {
        return Conexion::obtener()
            ->query('SELECT id_servicio, nombre FROM servicio ORDER BY nombre')
            ->fetchAll();
    }

    public function obtenerPorId(int $id): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM documento WHERE id_documento = :id');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    // Crea el documento y su codigo QR (los dos o ninguno)
    public function crear(string $titulo, string $archivoUrl, int $idAdmin, int $idServicio): int {
        $pdo = Conexion::obtener();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO documento (titulo, archivo_url, fecha_carga, id_admin_carga, id_servicio)
                 VALUES (:titulo, :archivo_url, CURDATE(), :id_admin, :id_servicio)'
            );
            $stmt->execute([
                ':titulo'      => $titulo,
                ':archivo_url' => $archivoUrl,
                ':id_admin'    => $idAdmin,
                ':id_servicio' => $idServicio,
            ]);
            $id = (int) $pdo->lastInsertId();

            $codigo = 'QR-DOC-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
            $qr = $pdo->prepare('INSERT INTO codigo_qr (codigo, id_documento) VALUES (:codigo, :id)');
            $qr->execute([':codigo' => $codigo, ':id' => $id]);

            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // Devuelve el codigo QR actual de un documento (o null si no tiene)
    public function obtenerQr(int $idDocumento): ?string {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT codigo FROM codigo_qr WHERE id_documento = :id ORDER BY id_qr DESC LIMIT 1');
        $stmt->execute([':id' => $idDocumento]);
        $codigo = $stmt->fetchColumn();
        return $codigo === false ? null : (string) $codigo;
    }

    // Al editar un documento se genera un QR nuevo y el anterior deja de funcionar
    public function actualizar(int $id, string $titulo, string $archivoUrl, int $idServicio): bool {
        $pdo = Conexion::obtener();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'UPDATE documento
                 SET titulo = :titulo, archivo_url = :archivo_url, id_servicio = :id_servicio
                 WHERE id_documento = :id'
            );
            $stmt->execute([
                ':titulo'      => $titulo,
                ':archivo_url' => $archivoUrl,
                ':id_servicio' => $idServicio,
                ':id'          => $id,
            ]);

            $borrar = $pdo->prepare('DELETE FROM codigo_qr WHERE id_documento = :id');
            $borrar->execute([':id' => $id]);

            $codigo = 'QR-DOC-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT)
                    . '-' . substr(md5(uniqid('', true)), 0, 6);
            $nuevo = $pdo->prepare('INSERT INTO codigo_qr (codigo, id_documento) VALUES (:codigo, :id)');
            $nuevo->execute([':codigo' => $codigo, ':id' => $id]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // Borra primero el QR (por la clave foranea) y despues el documento
    public function eliminar(int $id): bool {
        $pdo = Conexion::obtener();
        $pdo->beginTransaction();
        try {
            $qr = $pdo->prepare('DELETE FROM codigo_qr WHERE id_documento = :id');
            $qr->execute([':id' => $id]);
            $doc = $pdo->prepare('DELETE FROM documento WHERE id_documento = :id');
            $doc->execute([':id' => $id]);
            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
