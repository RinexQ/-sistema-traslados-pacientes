<?php
require_once __DIR__ . '/Conexion.php';

class DocumentoDAO {

    public function listarTodos(?string $busqueda = null): array {
        $pdo = Conexion::obtener();
        $sql = 'SELECT d.id_documento, d.titulo, d.archivo_url, d.fecha_carga,
                       a.nombre AS admin_nombre,
                       (SELECT q.codigo FROM codigo_qr q
                         WHERE q.id_documento = d.id_documento
                         ORDER BY q.id_qr LIMIT 1) AS codigo_qr
                FROM documento d
                JOIN administrativo a ON a.id_admin = d.id_admin_carga';
        $params = [];
        if ($busqueda !== null && $busqueda !== '') {
            $sql .= ' WHERE d.titulo LIKE :q';
            $params[':q'] = '%' . $busqueda . '%';
        }
        $sql .= ' ORDER BY d.fecha_carga DESC, d.id_documento DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM documento WHERE id_documento = :id');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function crear(string $titulo, string $archivoUrl, int $idAdmin): int {
        $pdo = Conexion::obtener();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO documento (titulo, archivo_url, fecha_carga, id_admin_carga)
                 VALUES (:titulo, :archivo_url, CURDATE(), :id_admin)'
            );
            $stmt->execute([
                ':titulo'      => $titulo,
                ':archivo_url' => $archivoUrl,
                ':id_admin'    => $idAdmin,
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

    public function actualizar(int $id, string $titulo, string $archivoUrl): bool {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            'UPDATE documento
             SET titulo = :titulo, archivo_url = :archivo_url
             WHERE id_documento = :id'
        );
        return $stmt->execute([
            ':titulo'      => $titulo,
            ':archivo_url' => $archivoUrl,
            ':id'          => $id,
        ]);
    }

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
