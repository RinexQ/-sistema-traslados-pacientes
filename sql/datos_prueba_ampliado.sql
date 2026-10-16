USE traslados_pacientes;

INSERT INTO administrativo (nombre, email) VALUES
('Ana Fernandez', 'ana.fernandez@sirius.com'),
('Carlos Rodriguez', 'carlos.rodriguez@sirius.com'),
('Laura Benitez', 'laura.benitez@sirius.com'),
('Martin Silva', 'martin.silva@sirius.com');

INSERT INTO paciente (ci, nombre, email, password_hash) VALUES
('11111111', 'Maria Gonzalez', 'maria.gonzalez@gmail.com', '$2b$12$WKs8BexiOfGlVHlrAZ5Gouf2LqXgKSirVDDlYkekhzuXeFnqN/V8C'),
('22222222', 'Jose Martinez', 'jose.martinez@gmail.com', ''),
('33333333', 'Lucia Perez', 'lucia.perez@gmail.com', ''),
('44444444', 'Roberto Acosta', 'roberto.acosta@gmail.com', ''),
('55555555', 'Silvia Ramos', 'silvia.ramos@gmail.com', ''),
('66666666', 'Federico Mena', 'federico.mena@gmail.com', '');

INSERT INTO conductor (nombre, licencia) VALUES
('Pedro Suarez', 'LIC-001'),
('Marta Diaz', 'LIC-002'),
('Gustavo Pintos', 'LIC-003'),
('Valeria Costa', 'LIC-004');

INSERT INTO vehiculo (patente, tipo, requiere_enfermero) VALUES
('ABC1234', 'Ambulancia basica', FALSE),
('XYZ5678', 'Ambulancia UCI', TRUE),
('DEF9012', 'Traslado programado', FALSE),
('GHI3456', 'Ambulancia UCI', TRUE);

INSERT INTO enfermero (nombre, matricula) VALUES
('Sofia Lopez', 'MAT-001'),
('Diego Torres', 'MAT-002'),
('Camila Ferreira', 'MAT-003'),
('Nicolas Vega', 'MAT-004');

INSERT INTO documento (titulo, archivo_url, id_admin_carga) VALUES
('Historia clinica - Maria Gonzalez', 'https://ejemplo.com/docs/historia_maria.pdf', 2),
('Autorizacion de traslado', 'https://ejemplo.com/docs/autorizacion.pdf', 2),
('Informe de estudios', 'https://ejemplo.com/docs/informe_estudios.pdf', 3),
('Indicaciones post alta - Jose Martinez', 'https://ejemplo.com/docs/indicaciones_jose.pdf', 3),
('Consentimiento informado', 'https://ejemplo.com/docs/consentimiento.pdf', 4),
('Horarios de visita sala 3', 'https://ejemplo.com/docs/horarios_sala3.pdf', 5);

INSERT INTO solicitud_traslado (fecha, origen, destino, estado, id_admin_solicitante) VALUES
('2026-09-01 08:30:00', 'Hospital Central', 'Domicilio - Maria Gonzalez', 'aprobada', 2),
('2026-09-02 10:00:00', 'Domicilio - Jose Martinez', 'Hospital Central', 'aprobada', 3),
('2026-09-03 09:15:00', 'Hospital Central', 'Centro de rehabilitacion', 'aprobada', 2),
('2026-09-04 13:00:00', 'Domicilio - Roberto Acosta', 'Hospital Central', 'aprobada', 4),
('2026-09-05 07:45:00', 'Hospital Central', 'Domicilio - Silvia Ramos', 'aprobada', 5),
('2026-09-06 11:30:00', 'Domicilio - Federico Mena', 'Hospital Central', 'pendiente', 3),
('2026-09-06 16:00:00', 'Hospital Central', 'Clinica privada', 'rechazada', 4);

INSERT INTO encuesta (titulo, es_anonima, id_admin_creador) VALUES
('Satisfaccion del servicio de traslado', FALSE, 2),
('Atencion del personal de enfermeria', TRUE, 3),
('Calidad de la informacion recibida', FALSE, 2);

INSERT INTO codigo_qr (codigo, id_documento) VALUES
('QR-DOC-001', 1),
('QR-DOC-002', 2),
('QR-DOC-003', 3),
('QR-DOC-004', 4),
('QR-DOC-005', 5),
('QR-DOC-006', 6);

INSERT INTO respuesta (id_encuesta, ci_paciente, respuesta_texto, fecha) VALUES
(1, '11111111', 'Muy buen servicio, el conductor fue muy amable.', '2026-09-01 12:00:00'),
(1, '22222222', 'Todo bien, pero el traslado se demoro un poco.', '2026-09-02 15:00:00'),
(1, '44444444', 'Llegaron puntuales y fueron muy cuidadosos.', '2026-09-04 18:00:00'),
(2, NULL, 'La enfermera explico todo con claridad.', '2026-09-02 16:30:00'),
(2, NULL, 'Buena atencion durante todo el viaje.', '2026-09-05 10:15:00'),
(3, '55555555', 'La informacion recibida fue clara y completa.', '2026-09-05 12:00:00');

INSERT INTO traslado (id_solicitud, id_conductor, id_enfermero, id_vehiculo, fecha_salida, hora_llegada, estado) VALUES
(1, 1, NULL, 1, '2026-09-01 09:00:00', '09:45:00', 'finalizado'),
(2, 2, 1, 2, '2026-09-02 10:30:00', NULL, 'programado'),
(3, 3, NULL, 3, '2026-09-03 09:45:00', NULL, 'en_curso'),
(4, 4, 2, 4, '2026-09-04 13:30:00', '14:20:00', 'finalizado'),
(5, 1, 3, 2, '2026-09-05 08:15:00', NULL, 'cancelado');

INSERT INTO carga (id_traslado, tipo_elemento, ci_paciente) VALUES
(1, 'Silla de ruedas', '11111111'),
(2, 'Tanque de oxigeno', '22222222'),
(3, 'Camilla', '33333333'),
(4, 'Monitor portatil', NULL),
(4, 'Silla de ruedas', '44444444'),
(5, 'Tanque de oxigeno', '55555555');
