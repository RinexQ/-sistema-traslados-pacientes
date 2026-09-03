<?php
// Capa de DATOS
// CRUD de la tabla "documento". Cada metodo hace una unica operacion SQL,
// usando siempre sentencias preparadas para evitar inyeccion SQL.

require_once __DIR__ . '/Conexion.php';

class DocumentoDAO {

    // SELECT: devuelve todos los documentos, con el nombre del admin que los cargo
    public function listarTodos(): array {
        $pdo = Conexion::obtener();
        $sql = 'SELECT d.id_documento, d.titulo, d.archivo_url, d.fecha_carga,
                       a.nombre AS admin_nombre
                FROM documento d
                JOIN administrativo a ON a.id_admin = d.id_admin_carga
                ORDER BY d.fecha_carga DESC';
        return $pdo->query($sql)->fetchAll();
    }

    // SELECT de un solo documento por id (para editar o para la vista de QR)
    public function obtenerPorId(int $id): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM documento WHERE id_documento = :id');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    // INSERT: alta de un documento nuevo
    public function crear(string $titulo, string $archivoUrl, int $idAdmin): int {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            'INSERT INTO documento (titulo, archivo_url, fecha_carga, id_admin_carga)
             VALUES (:titulo, :archivo_url, CURDATE(), :id_admin)'
        );
        $stmt->execute([
            ':titulo'      => $titulo,
            ':archivo_url' => $archivoUrl,
            ':id_admin'    => $idAdmin,
        ]);
        return (int) $pdo->lastInsertId();
    }

    // UPDATE: edicion de un documento existente
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

    // DELETE: baja fisica de un documento
    public function eliminar(int $id): bool {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('DELETE FROM documento WHERE id_documento = :id');
        return $stmt->execute([':id' => $id]);
    }
}
