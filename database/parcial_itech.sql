-- =========================================================
-- Base de datos: parcial_itech
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS parcial_itech;
CREATE DATABASE parcial_itech
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE parcial_itech;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- Tabla de países
-- =========================================================
CREATE TABLE paises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    CONSTRAINT uk_paises_nombre UNIQUE (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO paises (nombre) VALUES
('Panamá'),
('Colombia'),
('Costa Rica'),
('México'),
('Estados Unidos'),
('España'),
('Argentina'),
('Chile'),
('Perú'),
('Venezuela');

-- =========================================================
-- Tabla de áreas de interés tecnológico
-- =========================================================
CREATE TABLE areas_interes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    CONSTRAINT uk_areas_interes_nombre UNIQUE (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO areas_interes (nombre) VALUES
('Desarrollo Web'),
('Inteligencia Artificial'),
('Ciberseguridad'),
('Desarrollo Móvil'),
('Cloud Computing'),
('Big Data'),
('IoT (Internet de las Cosas)'),
('Blockchain'),
('DevOps'),
('Machine Learning');

-- =========================================================
-- Tabla principal de inscriptores
-- =========================================================
CREATE TABLE inscriptores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identidad VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    edad INT NOT NULL,
    sexo ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
    pais_residencia_id INT NOT NULL,
    nacionalidad_id INT NOT NULL,
    correo VARCHAR(150) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    observaciones TEXT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uk_inscriptores_identidad UNIQUE (identidad),
    CONSTRAINT uk_inscriptores_correo UNIQUE (correo),
    CONSTRAINT chk_inscriptores_edad CHECK (edad BETWEEN 1 AND 120),

    CONSTRAINT fk_inscriptores_pais_residencia
        FOREIGN KEY (pais_residencia_id)
        REFERENCES paises(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_inscriptores_nacionalidad
        FOREIGN KEY (nacionalidad_id)
        REFERENCES paises(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_inscriptores_identidad (identidad),
    INDEX idx_inscriptores_pais_residencia (pais_residencia_id),
    INDEX idx_inscriptores_nacionalidad (nacionalidad_id),
    INDEX idx_inscriptores_fecha_registro (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Tabla intermedia para temas tecnológicos
-- =========================================================
CREATE TABLE inscriptor_temas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inscriptor_id INT NOT NULL,
    area_interes_id INT NOT NULL,

    CONSTRAINT uk_inscriptor_temas_inscriptor_area
        UNIQUE (inscriptor_id, area_interes_id),

    CONSTRAINT fk_inscriptor_temas_inscriptor
        FOREIGN KEY (inscriptor_id)
        REFERENCES inscriptores(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_inscriptor_temas_area
        FOREIGN KEY (area_interes_id)
        REFERENCES areas_interes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_inscriptor_temas_inscriptor (inscriptor_id),
    INDEX idx_inscriptor_temas_area (area_interes_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Tabla adicional de firma digital
-- =========================================================
CREATE TABLE firmas_digitales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inscriptor_id INT NOT NULL,
    firma_digital LONGTEXT NOT NULL,
    algoritmo VARCHAR(60) NOT NULL DEFAULT 'OPENSSL_SHA256_RSA',
    fecha_firma TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uk_firmas_digitales_inscriptor UNIQUE (inscriptor_id),

    CONSTRAINT fk_firmas_digitales_inscriptor
        FOREIGN KEY (inscriptor_id)
        REFERENCES inscriptores(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_firmas_digitales_fecha (fecha_firma)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Usuario exclusivo de la aplicación
-- =========================================================
CREATE USER IF NOT EXISTS 'itech_app'@'localhost'
IDENTIFIED BY 'ItechApp2026*';

ALTER USER 'itech_app'@'localhost'
IDENTIFIED BY 'ItechApp2026*';

GRANT SELECT ON parcial_itech.paises
TO 'itech_app'@'localhost';

GRANT SELECT ON parcial_itech.areas_interes
TO 'itech_app'@'localhost';

GRANT SELECT, INSERT ON parcial_itech.inscriptores
TO 'itech_app'@'localhost';

GRANT SELECT, INSERT ON parcial_itech.inscriptor_temas
TO 'itech_app'@'localhost';

GRANT SELECT, INSERT ON parcial_itech.firmas_digitales
TO 'itech_app'@'localhost';

SHOW GRANTS FOR 'itech_app'@'localhost';
