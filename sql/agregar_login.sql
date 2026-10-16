
USE traslados_pacientes;

ALTER TABLE administrativo
    ADD COLUMN password_hash VARCHAR(255) NOT NULL DEFAULT '' AFTER email;

ALTER TABLE paciente
    ADD COLUMN password_hash VARCHAR(255) NOT NULL DEFAULT '' AFTER email;

INSERT INTO administrativo (nombre, email, password_hash)
VALUES (
    'Administrador de prueba',
    'admin@sirius.com',
    '$2b$12$7MhwefqHDvNBKAEEcGMLQu.enxv1ZBdAJm74yaRVonQewg6i3VSOS'
);
