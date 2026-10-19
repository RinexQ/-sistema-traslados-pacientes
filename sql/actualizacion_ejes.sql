USE traslados_pacientes;

CREATE TABLE servicio (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO servicio (nombre) VALUES
('Unidad de Cuidados Paliativos'),
('Emergencia'),
('Neonatología'),
('Cardiología'),
('Centro cardiovascular - Hemodinamia'),
('Centro cardiovascular - Crujía cardíaca'),
('Clínica médica A'),
('Clínica médica B'),
('Clínica médica C'),
('Dermatología'),
('Endocrinología'),
('Gastroenterología'),
('Gastroenterología - Unidad de ostomías'),
('Geriatría'),
('Hematología'),
('Infectología'),
('Medicina física, rehabilitación y medicina del deporte'),
('Nefrología'),
('Neurología'),
('Oncología'),
('Psiquiatría'),
('U.E. Autoinmunes sistémicas'),
('Unidad de tabaquismo'),
('Anestesiología'),
('Cirugía plástica y quemados'),
('Cirugía vascular periférica'),
('Ginecotocológica B'),
('Neurocirugía'),
('Odontología'),
('Oftalmología'),
('Otorrinolaringología'),
('Quirúrgica A'),
('Quirúrgica B'),
('Quirúrgica F'),
('Urología'),
('Traumatología y ortopedia de adultos'),
('Radioterapia'),
('Medicina Nuclear'),
('Hemoterapia'),
('Imagenología'),
('Información general');

ALTER TABLE documento ADD COLUMN id_servicio INT NULL;

UPDATE documento
SET id_servicio = (SELECT id_servicio FROM servicio WHERE nombre = 'Información general');

UPDATE documento
SET id_servicio = (SELECT id_servicio FROM servicio WHERE nombre = 'Imagenología')
WHERE titulo = 'Informe de estudios';

UPDATE documento
SET id_servicio = (SELECT id_servicio FROM servicio WHERE nombre = 'Clínica médica A')
WHERE titulo = 'Indicaciones post alta - Jose Martinez';

ALTER TABLE documento MODIFY id_servicio INT NOT NULL;

ALTER TABLE documento
    ADD CONSTRAINT fk_documento_servicio FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio);

ALTER TABLE administrativo
    ADD COLUMN rol ENUM('administrador','medico','enfermeria') NOT NULL DEFAULT 'administrador' AFTER email;

INSERT INTO administrativo (nombre, email, rol, password_hash) VALUES
('Dr. Ricardo Alonso', 'medico@sirius.com', 'medico', '$2b$12$Gk1ROD0lauoeoJPEM2lR6uNPpGVdfXNsKuqoXvslAsh4cTchQ1edS'),
('Enf. Paula Vidal', 'enfermeria@sirius.com', 'enfermeria', '$2b$12$ypXIrhj5CMKVihS1wAyqJOpgHlFCY8gj20DcIcg9afU6bkT/Yhf0W');

INSERT INTO documento (titulo, archivo_url, id_admin_carga, id_servicio) VALUES
('Indicaciones de interrupción voluntaria del embarazo', 'https://ejemplo.com/docs/interrupcion_voluntaria_embarazo.pdf',
 (SELECT id_admin FROM administrativo WHERE email = 'admin@sirius.com'),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Ginecotocológica B')),
('Prostatectomía radical (indicaciones e información para el paciente)', 'https://ejemplo.com/docs/prostatectomia_radical.pdf',
 (SELECT id_admin FROM administrativo WHERE email = 'admin@sirius.com'),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Urología')),
('Preparación para estudios imagenológicos', 'https://ejemplo.com/docs/preparacion_estudios_imagenologicos.pdf',
 (SELECT id_admin FROM administrativo WHERE email = 'admin@sirius.com'),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Imagenología')),
('Estudios diagnósticos con pertecnetato', 'https://ejemplo.com/docs/estudios_pertecnetato.pdf',
 (SELECT id_admin FROM administrativo WHERE email = 'admin@sirius.com'),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Medicina Nuclear')),
('Información sobre centellograma de perfusión miocárdica', 'https://ejemplo.com/docs/centellograma_perfusion_miocardica.pdf',
 (SELECT id_admin FROM administrativo WHERE email = 'admin@sirius.com'),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Medicina Nuclear')),
('Indicaciones ecocardiograma con dobutamina', 'https://ejemplo.com/docs/ecocardiograma_dobutamina.pdf',
 (SELECT id_admin FROM administrativo WHERE email = 'admin@sirius.com'),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Cardiología')),
('Indicaciones para pacientes en tratamiento con warfarina', 'https://ejemplo.com/docs/tratamiento_warfarina.pdf',
 (SELECT id_admin FROM administrativo WHERE email = 'admin@sirius.com'),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Cardiología')),
('Indicaciones ecocardiograma transesofágico', 'https://ejemplo.com/docs/ecocardiograma_transesofagico.pdf',
 (SELECT id_admin FROM administrativo WHERE email = 'admin@sirius.com'),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Cardiología')),
('Pauta para pacientes ostomizados (7 páginas)', 'https://ejemplo.com/docs/pauta_pacientes_ostomizados.pdf',
 (SELECT id_admin FROM administrativo WHERE email = 'admin@sirius.com'),
 (SELECT id_servicio FROM servicio WHERE nombre = 'Gastroenterología - Unidad de ostomías'));

INSERT INTO codigo_qr (codigo, id_documento)
SELECT CONCAT('QR-DOC-', LPAD(id_documento, 3, '0')), id_documento
FROM documento
WHERE id_documento NOT IN (SELECT id_documento FROM codigo_qr);

ALTER TABLE solicitud_traslado
    ADD COLUMN tipo ENUM('paciente','biologico','no_biologico') NOT NULL DEFAULT 'paciente' AFTER fecha,
    ADD COLUMN ci_paciente VARCHAR(20) NULL,
    ADD COLUMN domicilio VARCHAR(200) NULL,
    ADD COLUMN telefono VARCHAR(30) NULL,
    ADD COLUMN diagnostico VARCHAR(255) NULL,
    ADD COLUMN motivo VARCHAR(255) NULL,
    ADD COLUMN aislamiento BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN aislamiento_detalle VARCHAR(150) NULL,
    ADD COLUMN rodado ENUM('ninguno','silla','camilla') NOT NULL DEFAULT 'ninguno',
    ADD COLUMN oxigeno BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN verificado BOOLEAN NOT NULL DEFAULT FALSE,
    ADD CONSTRAINT fk_solicitud_paciente FOREIGN KEY (ci_paciente) REFERENCES paciente(ci);

UPDATE solicitud_traslado
SET ci_paciente = '11111111', domicilio = 'Av. Italia 2345', telefono = '099111222',
    diagnostico = 'Postoperatorio estable', motivo = 'Alta con traslado a domicilio',
    rodado = 'silla', verificado = TRUE
WHERE id_solicitud = 1;

UPDATE solicitud_traslado
SET ci_paciente = '22222222', domicilio = 'Bulevar Artigas 1450', telefono = '098333444',
    diagnostico = 'Control cardiologico', motivo = 'Traslado al hospital para control',
    rodado = 'camilla', oxigeno = TRUE, verificado = TRUE
WHERE id_solicitud = 2;

INSERT INTO solicitud_traslado
    (fecha, tipo, ci_paciente, origen, destino, domicilio, telefono, diagnostico, motivo,
     aislamiento, aislamiento_detalle, rodado, oxigeno, verificado, estado, id_admin_solicitante)
VALUES
(NOW(), 'paciente', '33333333', 'Hospital de Clínicas', 'Domicilio - Lucia Perez',
 'Calle Mercedes 987', '094555666', 'Infeccion respiratoria en recuperacion', 'Alta medica con traslado',
 TRUE, 'Aislamiento de contacto', 'silla', FALSE, TRUE, 'pendiente',
 (SELECT id_admin FROM administrativo WHERE email = 'enfermeria@sirius.com'));

INSERT INTO solicitud_traslado
    (fecha, tipo, origen, destino, motivo, estado, id_admin_solicitante)
VALUES
(NOW(), 'biologico', 'Laboratorio central', 'Banco de sangre', 'Traslado de muestras de sangre', 'pendiente',
 (SELECT id_admin FROM administrativo WHERE email = 'enfermeria@sirius.com')),
(NOW(), 'no_biologico', 'Farmacia del hospital', 'Policlinica externa', 'Entrega de insumos medicos', 'pendiente',
 (SELECT id_admin FROM administrativo WHERE email = 'medico@sirius.com'));
