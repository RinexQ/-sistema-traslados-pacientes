
USE traslados_pacientes;

INSERT INTO administrativo (nombre, email, password_hash) VALUES
('Ana Fernandez', 'ana.fernandez@sirius.com', ''),
('Carlos Rodriguez', 'carlos.rodriguez@sirius.com', '');

INSERT INTO paciente (ci, nombre, email, password_hash) VALUES
('11111111', 'Maria Gonzalez', 'maria.gonzalez@gmail.com', '$2b$12$WKs8BexiOfGlVHlrAZ5Gouf2LqXgKSirVDDlYkekhzuXeFnqN/V8C'),
('22222222', 'Jose Martinez', 'jose.martinez@gmail.com', ''),
('33333333', 'Lucia Perez', 'lucia.perez@gmail.com', '');

INSERT INTO conductor (nombre, licencia) VALUES
('Pedro Suarez', 'LIC-001'),
('Marta Diaz', 'LIC-002');

INSERT INTO vehiculo (patente, tipo, requiere_enfermero) VALUES
('ABC1234', 'Ambulancia basica', FALSE),
('XYZ5678', 'Ambulancia UCI', TRUE);

INSERT INTO enfermero (nombre, matricula) VALUES
('Sofia Lopez', 'MAT-001'),
('Diego Torres', 'MAT-002');

INSERT INTO documento (titulo, archivo_url, id_admin_carga) VALUES
('Historia clinica - Maria Gonzalez', 'https://ejemplo.com/docs/historia_maria.pdf', 1),
('Autorizacion de traslado', 'https://ejemplo.com/docs/autorizacion.pdf', 1),
('Informe de estudios', 'https://ejemplo.com/docs/informe_estudios.pdf', 2);

INSERT INTO solicitud_traslado (fecha, origen, destino, estado, id_admin_solicitante) VALUES
('2026-09-01 08:30:00', 'Hospital Central', 'Domicilio - Maria Gonzalez', 'aprobada', 1),
('2026-09-02 10:00:00', 'Domicilio - Jose Martinez', 'Hospital Central', 'pendiente', 2);

INSERT INTO encuesta (titulo, es_anonima, id_admin_creador) VALUES
('Satisfaccion del servicio de traslado', FALSE, 1);

INSERT INTO codigo_qr (codigo, id_documento) VALUES
('QR-DOC-001', 1),
('QR-DOC-002', 2),
('QR-DOC-003', 3);

INSERT INTO respuesta (id_encuesta, ci_paciente, respuesta_texto, fecha) VALUES
(1, '11111111', 'Muy buen servicio, el conductor fue muy amable.', '2026-09-01 12:00:00'),
(1, '22222222', 'Todo bien, pero el traslado se demoro un poco.', '2026-09-02 15:00:00');

INSERT INTO traslado (id_solicitud, id_conductor, id_enfermero, id_vehiculo, fecha_salida, hora_llegada, estado) VALUES
(1, 1, NULL, 1, '2026-09-01 09:00:00', '09:45:00', 'finalizado'),
(2, 2, 1, 2, '2026-09-02 10:30:00', NULL, 'programado');

INSERT INTO carga (id_traslado, tipo_elemento, ci_paciente) VALUES
(1, 'Silla de ruedas', '11111111'),
(2, 'Tanque de oxigeno', '22222222');
