
CREATE DATABASE IF NOT EXISTS traslados_pacientes
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE traslados_pacientes;

CREATE TABLE ADMINISTRATIVO (
    id_admin   INT AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE
);

CREATE TABLE PACIENTE (
    ci      VARCHAR(20) PRIMARY KEY,
    nombre  VARCHAR(100) NOT NULL,
    email   VARCHAR(150) NOT NULL UNIQUE
);

CREATE TABLE CONDUCTOR (
    id_conductor INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(100) NOT NULL,
    licencia     VARCHAR(50)  NOT NULL UNIQUE
);

CREATE TABLE VEHICULO (
    id_vehiculo        INT AUTO_INCREMENT PRIMARY KEY,
    patente            VARCHAR(20)  NOT NULL UNIQUE,
    tipo               VARCHAR(50)  NOT NULL,
    requiere_enfermero BOOLEAN      NOT NULL DEFAULT FALSE
);

CREATE TABLE ENFERMERO (
    id_enfermero INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(100) NOT NULL,
    matricula    VARCHAR(50)  NOT NULL UNIQUE
);

CREATE TABLE DOCUMENTO (
    id_documento    INT AUTO_INCREMENT PRIMARY KEY,
    titulo          VARCHAR(200) NOT NULL,
    archivo_url     VARCHAR(500) NOT NULL,
    fecha_carga     DATE         NOT NULL DEFAULT (CURRENT_DATE),
    id_admin_carga  INT NOT NULL,
    FOREIGN KEY (id_admin_carga) REFERENCES ADMINISTRATIVO(id_admin)
);

CREATE TABLE SOLICITUD_TRASLADO (
    id_solicitud          INT AUTO_INCREMENT PRIMARY KEY,
    fecha                 DATETIME NOT NULL,
    origen                VARCHAR(200) NOT NULL,
    destino               VARCHAR(200) NOT NULL,
    estado                ENUM('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
    id_admin_solicitante  INT NOT NULL,
    FOREIGN KEY (id_admin_solicitante) REFERENCES ADMINISTRATIVO(id_admin)
);

CREATE TABLE ENCUESTA (
    id_encuesta       INT AUTO_INCREMENT PRIMARY KEY,
    titulo            VARCHAR(200) NOT NULL,
    es_anonima        BOOLEAN      NOT NULL DEFAULT FALSE,
    id_admin_creador  INT NOT NULL,
    FOREIGN KEY (id_admin_creador) REFERENCES ADMINISTRATIVO(id_admin)
);

CREATE TABLE CODIGO_QR (
    id_qr        INT AUTO_INCREMENT PRIMARY KEY,
    codigo       VARCHAR(255) NOT NULL UNIQUE,
    id_documento INT NOT NULL,
    FOREIGN KEY (id_documento) REFERENCES DOCUMENTO(id_documento)
);

CREATE TABLE RESPUESTA (
    id_respuesta     INT AUTO_INCREMENT PRIMARY KEY,
    id_encuesta      INT NOT NULL,
    ci_paciente      VARCHAR(20) NULL,
    respuesta_texto  TEXT NOT NULL,
    fecha            DATETIME NOT NULL,
    FOREIGN KEY (id_encuesta) REFERENCES ENCUESTA(id_encuesta),
    FOREIGN KEY (ci_paciente) REFERENCES PACIENTE(ci)
);

CREATE TABLE TRASLADO (
    id_traslado   INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud  INT NOT NULL,
    id_conductor  INT NOT NULL,
    id_enfermero  INT NULL,
    id_vehiculo   INT NOT NULL,
    fecha_salida  DATETIME NOT NULL,
    hora_llegada  TIME NULL,
    estado        ENUM('programado','en_curso','finalizado','cancelado') NOT NULL DEFAULT 'programado',
    FOREIGN KEY (id_solicitud) REFERENCES SOLICITUD_TRASLADO(id_solicitud),
    FOREIGN KEY (id_conductor) REFERENCES CONDUCTOR(id_conductor),
    FOREIGN KEY (id_enfermero) REFERENCES ENFERMERO(id_enfermero),
    FOREIGN KEY (id_vehiculo) REFERENCES VEHICULO(id_vehiculo)
);

CREATE TABLE CARGA (
    id_carga      INT AUTO_INCREMENT PRIMARY KEY,
    id_traslado   INT NOT NULL,
    tipo_elemento VARCHAR(100) NOT NULL,
    ci_paciente   VARCHAR(20) NULL,
    FOREIGN KEY (id_traslado) REFERENCES TRASLADO(id_traslado),
    FOREIGN KEY (ci_paciente) REFERENCES PACIENTE(ci)
);
