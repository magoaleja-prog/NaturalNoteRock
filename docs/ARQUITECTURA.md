# ARQUITECTURA DEL PROYECTO

## Visión general
Natural Note Rock sigue una arquitectura **modular por capas**: capa de presentación (HTML/CSS/JS), capa de aplicación (PHP OOP), capa de acceso a datos (PDO/JDBC) y capa de datos (MySQL). También incluye una aplicación desktop Java con la misma fuente de datos.

## 1. Diagrama conceptual

```
┌─────────────────────────────────────────────────────────────────────────┐
│                            PRESENTACIÓN WEB                              │
│  HTML5 · CSS3 · JS Vanilla (garden, music, slider, games, polling API)   │
└─────────────────────────────────┬─────────────────────────────────────────┘
                                  │ HTTP/JSON
┌─────────────────────────────────▼─────────────────────────────────────────┐
│                          CAPA APLICACIÓN PHP                             │
│  index/login/logout · includes · api · admin/profesor/estudiante           │
│  config/config.php · src/ (Core/Models/Interfaces/Support/Auth)            │
└─────────────────────────────────┬─────────────────────────────────────────┘
                                  │ PDO (utf8mb4)
┌─────────────────────────────────▼─────────────────────────────────────────┐
│                               BASE DE DATOS                                │
│  MySQL/MariaDB · natural_note_rock (8 tablas) · db.properties (COMPARTIDO) │
└─────────────────────────────────┬─────────────────────────────────────────┘
                                  │ JDBC
┌─────────────────────────────────▼─────────────────────────────────────────┐
│                           APLICACIÓN DESKTOP JAVA                          │
│  modelo/dao/servicios/ConexionBD (Singleton) · Main (consola/reportes)     │
└─────────────────────────────────────────────────────────────────────────┘
```

## 2. Estructura de carpetas detallada

| Carpeta | Propósito |
|---|---|
| `config/` | `config.php` (autoloader, constantes, sesión, helpers) y configuración. |
| `src/Core/` | `Model.php` (abstracta base) y `Database.php` (Singleton PDO). |
| `src/Interfaces/` | `Autenticable.php`, `Notificable.php` (contratos de abstracción). |
| `src/Models/` | Entidades: `Usuario` (abstract), `Administrador`, `Profesor`, `Estudiante`, `Materia`, `Nota`, `Inasistencia`, `Sesion`, `Notificacion`. |
| `src/Support/` | `Auth.php` (fachada sesión/auditoría), `Jardin.php` (traducción nota→jardín), `Contenido.php` (recursos/juegos). |
| `includes/` | `header.php`, `footer.php`, `guard.php` (layout compartido + protección). |
| `assets/css/` | `style.css` (tema rock+naturaleza), `garden.css` (jardín/reproductor). |
| `assets/js/` | `main.js` (polling notificaciones + toasts), `garden.js` (canvas animado), `slider.js` (barra deslizante), `music.js` (Web Audio motor reutilizable), `games.js` (trivia/memorama). |
| `assets/img/` | SVGs (favicon, avatar por defecto). |
| `api/` | `notificaciones.php` (JSON GET/POST). |
| `admin/` | Dashboard, usuarios, materias, reportes. |
| `profesor/` | Dashboard, notas (slider), inasistencias, estudiantes. |
| `estudiante/` | Dashboard (placas), materia (jardín), perfil (upload), aprendizaje, música, juegos. |
| `uploads/avatares/` | Fotos de perfil subidas (protegido con `.htaccess`). |
| `database/` | `natural_note_rock.sql` + `db.properties` (COMPARTIDO PHP+Java). |
| `java/src/uco/naturalnoterock/` | `ConexionBD`, `modelo/`, `dao/`, `servicios/`, `Main`. |
| `java/lib/` | `mysql-connector-j-8.2.0.jar`. |
| `docs/` | Guías, manual, arquitectura, explicación POO. |

## 3. Diseño de base de datos

Tablas con integridad referencial (FK + CASCADE donde aplica):

| Tabla | Descripción |
|---|---|
| `usuarios` | Entidad base (herencia). Campos: id, nombre, apellido, username(email únicos), password_hash (bcrypt), rol, foto, activo, timestamps, último_acceso. |
| `estudiantes` | Extiende usuarios (1:1). codigo, documento, curso, fecha_nacimiento, acudiente, telefono, direccion. FK usuario_id CASCADE. |
| `profesores` | Extiende usuarios (1:1). codigo, especialidad, titulo, telefono. FK usuario_id CASCADE. |
| `materias` | 12 materias con color, icono, área, descripción, activo. |
| `notas` | Calificaciones 1.0–5.0. FK estudiante_id, materia_id, profesor_id (CASCADE). |
| `inasistencias` | Control de ausencias/tardanzas/excusas. Campos tipo, justificada, observacion, fecha. |
| `sesiones` | Auditoría de logins: token, ip, user_agent, activa, creado_en, cierre_en. FK usuario_id CASCADE. |
| `notificaciones` | Avisos por usuario: titulo, mensaje, tipo, url, leida, creado_en. FK usuario_id CASCADE. Índice (usuario_id, leida). |

## 4. Patrones utilizados

| Patrón | Implementación |
|---|---|
| **Singleton** | `App\Core\Database` (PHP) y `ConexionBD` (Java). Garantiza una única conexión. |
| **Factory (simple)** | `Usuario::desdeFila()` crea la subclase correcta según `rol`. |
| **Fachada (Facade)** | `App\Support\Auth` centraliza sesión, validación de roles, inicio/cierre. |
| **DAO (Data Access Object)** | Java: `EstudianteDAO`, `NotaDAO` separan SQL de modelo/servicio. PHP usa modelos heredados de `Model` (más compacto). |
| **MVC ligero** | Separación: vistas (páginas .php), controladores lógicos (clases Auth/Model), modelos (entidades). |

## 5. Seguridad (básica)

- **Autenticación obligatoria**: todas las páginas privadas exigen login vía `guard.php`/`Auth`.
- **Autorización por rol**: `Auth::requerirRol()` protege rutas (`admin/*`, `profesor/*`, `estudiante/*`).
- **Contraseñas**: `password_hash()` con bcrypt; verificación con `password_verify()`.
- **CSRF mínimo**: formularios POST usan validación de acción + confirmaciones. Para fines académicos es suficiente.
- **Subida de archivos**: validación MIME vía `getimagesize()`, extensión permitida, límite 2MB, nombres aleatorios, solo imágenes.
- **Protección de carpetas**: `.htaccess` bloquea acceso directo a `config/`, `src/`, `database/`, `includes/`, `docs/`. `uploads/.htaccess` impide ejecución PHP.
- **Sesiones PHP seguras**: `session_regenerate_id(true)` al login, `httponly`, `samesite=Lax`.
- **Escapado HTML**: helper `e()` usa `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- **Consultas preparadas**: 100% con prepared statements (PDO + JDBC) → protección contra SQL injection.

## 6. Frontend (UI/UX)

- **Tema rock + naturaleza**: colores oscuros, gradientes metálicos, tornillos en placas, tipografías impactantes (Metal Mania/Oswald/Nunito).
- **Componentes**: `.placa` metálica con color por materia, `.card`, `.btn`, `.tabla`, `.campana` con contador animado.
- **Jardín Vivo (Canvas)**: dibuja cielo/suelo, plantas con balanceo, flores/frutos, aves/abejas, plagas. Apariencia cambia por `estado` y `factor` derivados de la nota.
- **Música sintética (Web Audio API)**: genera riffs + batería con osciladores, distorsión y filtro. Sin archivos `.mp3` (evita binarios innecesarios).
- **Barra deslizante animada**: `input[type=range]` + barra de progreso + animación `requestAnimationFrame` al cargar/confirmar.
- **Notificaciones "tiempo real"**: polling `fetch` cada 8s a `api/notificaciones.php`. Actualiza contador + muestra toasts al detectar nuevas.
- **Responsive**: sidebar colapsable en móvil.

## 7. Java (Desktop) - Conexión compartida

- **`db.properties` único**: `database/db.properties` es leído por PHP y por Java → **misma fuente de verdad**.
- **Singleton JDBC**: `ConexionBD.getInstancia()` garantiza reutilización lógica.
- **Modelo espejo**: clases `Usuario` (abstract), `Estudiante`, `Profesor`, `Nota` replican conceptos del dominio PHP (demostrando herencia/polimorfismo/abstracción/encapsulamiento en Java).
- **DAO + Servicio**: separación clara. `ReporteAcademico` imprime reporte consolidado (promedios por materia, jardín académico por estudiante, detalle de notas).
- **UTF-8**: conexión JDBC con `useUnicode=true&characterEncoding=UTF-8` para coherencia con MySQL utf8mb4.

## 8. Flujo de datos ejemplo (registrar nota)

1. Profesor accede `profesor/notas.php`, selecciona estudiante/materia/período, mueve barra deslizante (JS actualiza UI).
2. POST → PHP valida → `Nota::registrar()` inserta en `notas` (transaccional implícita).
3. `Nota::registrar()` obtiene `usuario_id` del estudiante → inserta notificación en `notificaciones` (tipo `nota`, url a materia).
4. Redirige con mensaje OK.
5. Estudiante ve contador aumentar (polling API) y toast. Al entrar a la materia, `Jardin::paraNota()` calcula estado y `garden.js` dibuja jardín; `music.js` elige modo según rendimiento.

## 9. Consideraciones finales

- **Sin frameworks**: Vanilla PHP/JS para mantener simpleza y cumplir con alcance académico.
- **Modular**: fácil añadir roles, materias o funcionalidades sin romper arquitectura.
- **Portable**: pensado para XAMPP (Windows). Rutas usan `BASE_PATH` dinámico.
- **Bien documentado**: README, guías, manual, explicación POO incluidos.
- **Demostrable**: web + desktop + conexión compartida + todos los pilares aplicados.
