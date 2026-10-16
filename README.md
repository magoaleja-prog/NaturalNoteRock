# Natural Note Rock · UCO

> _Tarea de Quipux con POO retomada utilizando la herramienta OpenCode, Git y varios lenguajes de programación._

Sistema académico educativo con fusión estética entre **rock** y **naturaleza**. Desarrollado con HTML, CSS, JavaScript, PHP, Java y MySQL, organizando la lógica bajo los **4 pilares de la Programación Orientada a Objetos (POO)**: encapsulamiento, herencia, polimorfismo y abstracción.

- **Organización educativa**: UCO
- **Tecnologías**: PHP 8.2, MySQL/MariaDB, Java 8+, HTML5/CSS3, JavaScript (Vanilla)
- **Base de datos compartida**: PHP y Java leen la misma configuración (`database/db.properties`)

## Características

- **Inicio de sesión obligatorio** para todos los usuarios.
- **Roles con permisos exclusivos**: `Administrador`, `Profesor` y `Estudiante`.
- **Sistema de notificaciones en tiempo real** (polling cada 8s). Alertas automáticas al subir notas o registrar inasistencias.
- **Jardín Vivo**: al ingresar a una materia, la nota se representa como un jardín animado en canvas (nota alta = florido; media = crecimiento moderado; baja = marchito con plagas).
- **Música rockera sintética** según el rendimiento (Web Audio API), sin archivos binarios.
- **Barra deslizante animada** para registrar notas (no se escribe el número; la barra avanza visualmente hasta el valor final).
- **Fotos de perfil** para estudiantes (subida y persistencia en sesión).
- **Secciones educativas**: aprendizaje con recursos reales por materia, reproductor de música con listas, juegos interactivos (Trivia Rockera y Memorama).

## Estructura del proyecto (htdocs)

```
NaturalNoteRock/
├── index.php, login.php, logout.php, .htaccess
├── config/                # Configuración PHP
├── src/                   # Clases OOP PHP (App\*)
├── includes/              # Cabecera, pie, guardas
├── assets/                # CSS/JS/IMG
├── admin/ profesor/ estudiante/  # Interfaces por rol
├── api/                   # APIs JSON
├── uploads/               # Fotos de perfil
├── database/              # SQL + db.properties (compartido PHP/Java)
├── java/                  # Aplicación desktop Java (JDBC)
├── docs/                  # Guías y manuales
├── opencode.json          # Configuración de OpenCode para el proyecto
├── OPENCODE.md            # Qué es OpenCode y cómo se usó
├── PROMPTS.md            # Registro de los prompts utilizados
└── README.md
```

## Credenciales de prueba

| Usuario    | Contraseña     | Rol           |
|------------|---------------|---------------|
| `admin`    | `admin123`    | Administrador |
| `profe.roca` | `profe123`    | Profesor      |
| `luna.roca`  | `estudiante123` | Estudiante    |
| `mateo.rock` | `estudiante123` | Estudiante    |
| `sara.bloom` | `estudiante123` | Estudiante    |

## Documentación

- [Guía de Instalación, Ejecución y Uso](docs/GUIA_INSTALACION.md)
- [Guía de Git (historial, ramas y mensajes de commit)](docs/GUIA_GIT.md)
- [Explicación de los 4 pilares de la POO](docs/PILARES_POO.md)
- [Manual de Usuario](docs/MANUAL_USUARIO.md)
- [Arquitectura del Proyecto](docs/ARQUITECTURA.md)
- [README Java](java/README.md)
- [OpenCode (herramienta de IA utilizada)](OPENCODE.md)
- [Prompts utilizados durante el desarrollo](PROMPTS.md)

## Licencia / Propósito

Proyecto desarrollado para la **Organización Educativa UCO** con fines académicos, siguiendo buenas prácticas de desarrollo (POO, Git, seguridad básica y organización modular).

¡Que viva el **rock** y la **naturaleza**! 🎸🌿
