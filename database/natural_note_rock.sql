-- =====================================================================
--  NATURAL NOTE ROCK  -  Base de datos
--  Organizacion educativa: UCO
--  Motor: MySQL / MariaDB (XAMPP)
--  Autor: Equipo Natural Note Rock
-- ---------------------------------------------------------------------
--  Este script crea por completo el esquema de datos del proyecto,
--  respetando la jerarquia de llaves foraneas y cargando datos de
--  demostracion (usuarios, materias, notas, inasistencias, avisos).
--
--  Ejecutar asi:
--     mysql -u root -p < database/natural_note_rock.sql
--  o importar desde phpMyAdmin.
-- =====================================================================

DROP DATABASE IF EXISTS natural_note_rock;
CREATE DATABASE natural_note_rock
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE natural_note_rock;

-- Fuerza la codificacion de la conexion del cliente (evita acentos rotos).
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. USUARIOS : tabla padre (herencia -> estudiantes y profesores)
--    Guarda la identidad y el rol. Los datos propios de cada rol
--    viven en sus tablas hijas (relacion 1:1).
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(80)  NOT NULL,
    apellido        VARCHAR(80)  NOT NULL,
    username        VARCHAR(50)  NOT NULL UNIQUE,
    email           VARCHAR(120) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    rol             ENUM('administrador','profesor','estudiante') NOT NULL,
    foto            VARCHAR(255) DEFAULT NULL,
    activo          TINYINT(1)   NOT NULL DEFAULT 1,
    ultimo_acceso   DATETIME     DEFAULT NULL,
    creado_en       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_usuarios_rol (rol)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. ESTUDIANTES : especializacion de usuarios (rol = estudiante)
-- ---------------------------------------------------------------------
CREATE TABLE estudiantes (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id       INT NOT NULL UNIQUE,
    codigo           VARCHAR(30) UNIQUE,
    documento        VARCHAR(30) UNIQUE,
    curso            VARCHAR(40),
    fecha_nacimiento DATE,
    acudiente        VARCHAR(120),
    telefono         VARCHAR(30),
    direccion        VARCHAR(180),
    CONSTRAINT fk_estudiante_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. PROFESORES : especializacion de usuarios (rol = profesor)
-- ---------------------------------------------------------------------
CREATE TABLE profesores (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id   INT NOT NULL UNIQUE,
    codigo       VARCHAR(30) UNIQUE,
    especialidad VARCHAR(100),
    titulo       VARCHAR(100),
    telefono     VARCHAR(30),
    CONSTRAINT fk_profesor_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. MATERIAS : las 12 areas academicas del colegio.
--    color/icono alimentan las "placas metalicas" del estudiante.
-- ---------------------------------------------------------------------
CREATE TABLE materias (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80) NOT NULL UNIQUE,
    codigo      VARCHAR(20) NOT NULL UNIQUE,
    area        VARCHAR(60),
    descripcion TEXT,
    color       VARCHAR(20) DEFAULT '#3a3f4a',
    icono       VARCHAR(10) DEFAULT '🎸',
    activo      TINYINT(1)  NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. NOTAS : calificaciones 1.0 - 5.0 (escala colombiana)
-- ---------------------------------------------------------------------
CREATE TABLE notas (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    materia_id    INT NOT NULL,
    profesor_id   INT NOT NULL,
    periodo       VARCHAR(20) NOT NULL DEFAULT 'Periodo 1',
    valor         DECIMAL(3,1) NOT NULL,
    observacion   VARCHAR(255),
    fecha         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_nota_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE CASCADE,
    CONSTRAINT fk_nota_materia    FOREIGN KEY (materia_id)    REFERENCES materias(id)   ON DELETE CASCADE,
    CONSTRAINT fk_nota_profesor   FOREIGN KEY (profesor_id)   REFERENCES profesores(id)  ON DELETE CASCADE,
    INDEX idx_notas_estudiante (estudiante_id, materia_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. INASISTENCIAS : control de ausencias / tardanzas / excusas
-- ---------------------------------------------------------------------
CREATE TABLE inasistencias (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    materia_id    INT NOT NULL,
    profesor_id   INT NOT NULL,
    fecha         DATE NOT NULL,
    tipo          ENUM('ausencia','tardanza','excusa') NOT NULL DEFAULT 'ausencia',
    justificada   TINYINT(1) NOT NULL DEFAULT 0,
    observacion   VARCHAR(255),
    creado_en     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_inasistencia_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE CASCADE,
    CONSTRAINT fk_inasistencia_materia    FOREIGN KEY (materia_id)    REFERENCES materias(id)   ON DELETE CASCADE,
    CONSTRAINT fk_inasistencia_profesor   FOREIGN KEY (profesor_id)   REFERENCES profesores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. SESIONES : auditoria de inicios de sesion (obligatorio loguearse)
-- ---------------------------------------------------------------------
CREATE TABLE sesiones (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token      VARCHAR(128) NOT NULL,
    ip         VARCHAR(45),
    user_agent VARCHAR(255),
    activa     TINYINT(1) NOT NULL DEFAULT 1,
    creado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cierre_en  DATETIME DEFAULT NULL,
    CONSTRAINT fk_sesion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_sesiones_activa (usuario_id, activa)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 8. NOTIFICACIONES : avisos en tiempo real por usuario
-- ---------------------------------------------------------------------
CREATE TABLE notificaciones (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo     VARCHAR(120) NOT NULL,
    mensaje    VARCHAR(255) NOT NULL,
    tipo       ENUM('nota','inasistencia','sistema','mensaje') NOT NULL DEFAULT 'sistema',
    url        VARCHAR(255) DEFAULT NULL,
    leida      TINYINT(1) NOT NULL DEFAULT 0,
    creado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notificacion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_notif_no_leidas (usuario_id, leida)
) ENGINE=InnoDB;

-- =====================================================================
--  DATOS DE DEMOSTRACION
--  Contrasenas (texto plano) para entrar al sistema:
--     admin       / admin123
--     profe.roca  / profe123
--     luna.roca   / estudiante123   (y los demas estudiantes)
-- =====================================================================

INSERT INTO usuarios (id, nombre, apellido, username, email, password_hash, rol) VALUES
(1, 'Root',  'UCO',    'admin',       'admin@uco.edu.co',       '$2y$10$O1n.oUb5/.TjzGZIyrxnuO5LZ6DuzS4EL0HoRvVBJaMilR67iQZW.', 'administrador'),
(2, 'Diego', 'Roca',   'profe.roca',  'diego.roca@uco.edu.co',  '$2y$10$U2Y7BKneJDmhw0UdY41scO1i3YtOfe4imhW8.oXQ5vbno/yBVlNtO', 'profesor'),
(3, 'Luna',  'Verde',  'luna.roca',   'luna.verde@uco.edu.co',  '$2y$10$ET7HkfBG2XMP9aCQ4.Pg0eTzVseNuq9I70Khqcdvh8U5xfdN1FARO', 'estudiante'),
(4, 'Mateo', 'Cordero', 'mateo.rock', 'mateo.cordero@uco.edu.co','$2y$10$ET7HkfBG2XMP9aCQ4.Pg0eTzVseNuq9I70Khqcdvh8U5xfdN1FARO', 'estudiante'),
(5, 'Sara',  'Florez', 'sara.bloom',  'sara.florez@uco.edu.co', '$2y$10$ET7HkfBG2XMP9aCQ4.Pg0eTzVseNuq9I70Khqcdvh8U5xfdN1FARO', 'estudiante');

INSERT INTO estudiantes (id, usuario_id, codigo, documento, curso, fecha_nacimiento, acudiente, telefono, direccion) VALUES
(1, 3, 'EST-2026-001', '1001001001', '11-A', '2008-03-14', 'Marta Verde',  '3001112233', 'Calle 12 #4-56'),
(2, 4, 'EST-2026-002', '1001001002', '11-A', '2008-07-22', 'Julio Cordero','3004445566', 'Carrera 9 #18-30'),
(3, 5, 'EST-2026-003', '1001001003', '11-B', '2009-01-09', 'Ana Florez',   '3007778899', 'Av. Libertad #77-10');

INSERT INTO profesores (id, usuario_id, codigo, especialidad, titulo, telefono) VALUES
(1, 2, 'DOC-2026-001', 'Ciencias Naturales y Quimica', 'Magister en Educacion', '3105556677');

INSERT INTO materias (id, nombre, codigo, area, descripcion, color, icono) VALUES
(1,  'Religion',      'RELIG', 'Humanidades',     'Formacion espiritual y valores para la convivencia.',      '#7e57c2', '🕊️'),
(2,  'Sociales',      'SOCIA', 'Ciencias Sociales','Historia, geografia y cultura de Colombia y el mundo.',    '#f9a825', '🌍'),
(3,  'Matematicas',   'MATEM', 'Ciencias Exactas', 'Pensamiento logico, algebra, calculo y geometria.',        '#1565c0', '➗'),
(4,  'Espanol',       'ESPAN', 'Lenguaje',        'Lectura critica, literatura y produccion textual.',        '#c62828', '📖'),
(5,  'Filosofia',     'FILOS', 'Humanidades',     'Pensamiento critico y grandes preguntas del ser.',         '#5d4037', '🦉'),
(6,  'Tecnologia',    'TECNO', 'Tecnologia',      'Informatica, programacion y cultura digital.',             '#00838f', '💻'),
(7,  'Media Tecnica', 'MEDIA', 'Tecnica',         'Formacion tecnica laboral articulada con el SENA.',        '#ef6c00', '🛠️'),
(8,  'Quimica',       'QUIMI', 'Ciencias Nat.',   'Materia, reacciones y laboratorio.',                       '#2e7d32', '🧪'),
(9,  'Naturales',     'NATUR', 'Ciencias Nat.',   'Biologia, ecologia y cuidado del planeta.',                '#558b2f', '🌿'),
(10, 'Politica',      'POLIT', 'Ciencias Sociales','Constitucion, democracia y participacion ciudadana.',     '#37474f', '🏛️'),
(11, 'Etica',         'ETICA', 'Humanidades',     'Decisiones, convivencia y proyecto de vida.',              '#ad1457', '⚖️'),
(12, 'Estadistica',   'ESTAD', 'Ciencias Exactas', 'Datos, probabilidad y analisis de informacion.',          '#6a1b9a', '📊');

-- Notas: estudiante 1 (Luna) destaca; 2 (Mateo) promedio; 3 (Sara) en riesgo.
INSERT INTO notas (estudiante_id, materia_id, profesor_id, periodo, valor, observacion) VALUES
(1, 1, 1, 'Periodo 1', 4.8, 'Excelente participacion y respeto.'),
(1, 2, 1, 'Periodo 1', 4.5, 'Domina los temas de historia.'),
(1, 3, 1, 'Periodo 1', 4.9, 'Razonamiento logico sobresaliente.'),
(1, 4, 1, 'Periodo 1', 4.6, 'Gran lectura critica.'),
(1, 5, 1, 'Periodo 1', 4.3, 'Aporta reflexiones profundas.'),
(1, 6, 1, 'Periodo 1', 5.0, 'Proyecto de tecnologia impecable.'),
(1, 7, 1, 'Periodo 1', 4.7, 'Muy responsable en taller.'),
(1, 8, 1, 'Periodo 1', 4.4, 'Buen trabajo de laboratorio.'),
(1, 9, 1, 'Periodo 1', 4.9, 'Lidera el proyecto de huerta.'),
(1, 10,1, 'Periodo 1', 4.2, 'Participa con criterio.'),
(1, 11,1, 'Periodo 1', 4.8, 'Ejemplo de convivencia.'),
(1, 12,1, 'Periodo 1', 4.5, 'Maneja bien los datos.'),

(2, 1, 1, 'Periodo 1', 3.5, 'Cumple, puede mejorar.'),
(2, 2, 1, 'Periodo 1', 3.2, 'Falta profundizar.'),
(2, 3, 1, 'Periodo 1', 3.8, 'Va por buen camino.'),
(2, 4, 1, 'Periodo 1', 3.0, 'Debe leer mas.'),
(2, 5, 1, 'Periodo 1', 3.4, 'Participacion irregular.'),
(2, 6, 1, 'Periodo 1', 4.0, 'Le gusta programar.'),
(2, 7, 1, 'Periodo 1', 3.6, 'Buen desempeno en taller.'),
(2, 8, 1, 'Periodo 1', 3.1, 'Cuidado con el laboratorio.'),
(2, 9, 1, 'Periodo 1', 3.7, 'Interes por la biologia.'),
(2, 10,1, 'Periodo 1', 3.3, 'Puede aportar mas.'),
(2, 11,1, 'Periodo 1', 3.9, 'Buena actitud.'),
(2, 12,1, 'Periodo 1', 3.5, 'Manejo basico de datos.'),

(3, 1, 1, 'Periodo 1', 2.4, 'Requiere acompanamiento.'),
(3, 2, 1, 'Periodo 1', 2.8, 'Faltan tareas.'),
(3, 3, 1, 'Periodo 1', 1.9, 'Dificultades en algebra.'),
(3, 4, 1, 'Periodo 1', 2.6, 'Comprension lectora baja.'),
(3, 5, 1, 'Periodo 1', 2.2, 'Poco interes.'),
(3, 6, 1, 'Periodo 1', 3.1, 'Se esfuerza en tecnologia.'),
(3, 7, 1, 'Periodo 1', 2.5, 'Inasistencias frecuentes.'),
(3, 8, 1, 'Periodo 1', 2.0, 'Riesgo de perder la materia.'),
(3, 9, 1, 'Periodo 1', 2.7, 'Puede recuperar.'),
(3, 10,1, 'Periodo 1', 2.3, 'Debe nivelar.'),
(3, 11,1, 'Periodo 1', 2.9, 'Actitud a mejorar.'),
(3, 12,1, 'Periodo 1', 2.1, 'Necesita refuerzo.');

INSERT INTO inasistencias (estudiante_id, materia_id, profesor_id, fecha, tipo, justificada, observacion) VALUES
(3, 7, 1, '2026-02-18', 'ausencia', 0, 'No asistio al taller tecnico.'),
(3, 3, 1, '2026-02-25', 'tardanza', 0, 'Llego 25 minutos tarde.'),
(3, 8, 1, '2026-03-04', 'ausencia', 1, 'Cita medica justificada.'),
(2, 4, 1, '2026-03-11', 'excusa',   1, 'Excusa presentada por el acudiente.');

INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, url, leida) VALUES
(3, 'Nueva nota en Matematicas', 'Tu nota de Matematicas es 4.9. ¡Tu jardin esta floreciendo!', 'nota', 'estudiante/materia.php?id=3', 0),
(3, 'Nueva nota en Naturales',   'Tu nota de Naturales es 4.9. ¡Sigue asi!',                    'nota', 'estudiante/materia.php?id=9', 0),
(4, 'Nueva nota en Espanol',     'Tu nota de Espanol es 3.0. ¡Puedes mejorar!',                 'nota', 'estudiante/materia.php?id=4', 0),
(5, 'Inasistencia registrada',   'Se registro una ausencia en Media Tecnica.',                  'inasistencia', 'estudiante/dashboard.php', 0),
(5, 'Alerta academica',          'Tu nota de Matematicas es 1.9. ¡Necesitas refuerzo!',         'nota', 'estudiante/materia.php?id=3', 0),
(2, 'Bienvenido profesor',       'Tienes estudiantes por calificar en el Periodo 1.',           'sistema', NULL, 1);

-- =====================================================================
--  Fin del script. ¡Que viva el rock y la naturaleza!
-- =====================================================================
