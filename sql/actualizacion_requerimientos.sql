USE traslados_pacientes;

ALTER TABLE administrativo ADD COLUMN ci VARCHAR(8) NULL AFTER nombre;

UPDATE administrativo SET ci = CAST(10000000 + id_admin AS CHAR);

ALTER TABLE administrativo MODIFY ci VARCHAR(8) NOT NULL;

ALTER TABLE administrativo ADD CONSTRAINT uq_administrativo_ci UNIQUE (ci);

ALTER TABLE respuesta ADD COLUMN calificacion TINYINT NULL;

UPDATE respuesta SET calificacion = 3 + (id_respuesta % 3);

ALTER TABLE vehiculo
    ADD COLUMN estado ENUM('disponible','roto') NOT NULL DEFAULT 'disponible';

INSERT INTO vehiculo (patente, tipo, requiere_enfermero, estado)
VALUES ('JKL7890', 'Auto de apoyo', FALSE, 'roto');

ALTER TABLE solicitud_traslado
    MODIFY tipo ENUM('paciente','biologico','no_biologico','organo','muestra_biologica','cadeteria','materiales') NOT NULL DEFAULT 'paciente',
    ADD COLUMN prioridad ENUM('sin_prioridad','organos','dialisis','tratamiento','alta_domicilio','horario_especifico') NOT NULL DEFAULT 'sin_prioridad';

UPDATE solicitud_traslado SET tipo = 'muestra_biologica' WHERE tipo = 'biologico';

UPDATE solicitud_traslado SET tipo = 'materiales' WHERE tipo = 'no_biologico';

ALTER TABLE solicitud_traslado
    MODIFY tipo ENUM('paciente','organo','muestra_biologica','cadeteria','materiales') NOT NULL DEFAULT 'paciente';

UPDATE solicitud_traslado SET prioridad = 'alta_domicilio' WHERE id_solicitud = 1;

UPDATE solicitud_traslado SET prioridad = 'tratamiento' WHERE id_solicitud = 2;

INSERT INTO solicitud_traslado
    (fecha, tipo, origen, destino, motivo, prioridad, estado, id_admin_solicitante)
VALUES
(NOW(), 'organo', 'Hospital de Clínicas', 'Centro de trasplante', 'Traslado de órgano para trasplante', 'organos', 'aprobada',
 (SELECT id_admin FROM administrativo WHERE rol = 'medico' ORDER BY id_admin LIMIT 1)),
(NOW(), 'cadeteria', 'Registros médicos', 'Mesa de entrada central', 'Entrega de trámites administrativos', 'sin_prioridad', 'pendiente',
 (SELECT id_admin FROM administrativo WHERE rol = 'enfermeria' ORDER BY id_admin LIMIT 1));

INSERT INTO documento (titulo, archivo_url, id_admin_carga, id_servicio)
VALUES
('Indicaciones de ejemplo (archivo local)', 'documentos/ejemplo_indicaciones.pdf',
 (SELECT id_admin FROM administrativo WHERE rol = 'administrador' ORDER BY id_admin LIMIT 1),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Información general'));

INSERT INTO codigo_qr (codigo, id_documento)
SELECT CONCAT('QR-DOC-', LPAD(id_documento, 3, '0')), id_documento
FROM documento
WHERE id_documento NOT IN (SELECT id_documento FROM codigo_qr);
