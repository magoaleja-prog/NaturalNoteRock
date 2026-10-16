# MANUAL DE USUARIO

## Introducción
Natural Note Rock es un sistema académico para la Organización Educativa UCO. Combina estética rockera y natural para hacer el seguimiento académico más atractivo y visual.

## Acceso al sistema
1. Abrir navegador: `http://localhost/NaturalNoteRock/`
2. Iniciar sesión con credenciales válidas (obligatorio para todos)
3. Al ingresar, el sistema redirige al panel correspondiente según tu rol.

## 1. ROL: ESTUDIANTE

### Panel principal (Mi jardín)
- Muestra tu foto de perfil, promedio general, materias e inasistencias.
- **Placas metálicas** por materia: cada una tiene color e ícono propios. Pulsa una placa para ver el jardín de esa materia.
- Estado general del jardín se indica debajo de tu saludo.

### Vista por materia (Jardín Vivo)
- **Nota numérica** visible en grande.
- **Jardín animado (canvas)** que representa tu rendimiento:
  - **Nota ≥ 4.0**: Jardín florecido → flores, frutos, aves e insectos polinizadores. Música rockera alegre y rápida.
  - **3.0 ≤ Nota < 4.0**: Crecimiento moderado → plantas sanas con algún elemento menor. Música rockera moderada.
  - **Nota < 3.0**: Plantas marchitas y plagas → necesita refuerzo. Música rockera melancólica (lenta, tono menor).
  - **Sin notas**: Jardín en espera (semillas).
- **Reproductor**: Botón Play/Pausa para escuchar la banda sonora generada en vivo según tu nota.
- **Historial**: Lista de todas las notas registradas en esa materia.

### Mi perfil
- Editar nombre, apellido, correo electrónico y contraseña (opcional).
- **Subir foto de perfil** (JPG/PNG/GIF/WEBP, máx. 2 MB). La foto aparece en todo el sistema mientras estás conectado.

### Aprendizaje
- Recursos reales organizados por materia: enlaces a Khan Academy, Colombia Aprende, RAE, SENA, Wikipedia, tablas periódicas, etc.
- Cada recurso incluye tipo (Curso, Libro, Portal, Herramienta) y breve descripción.

### Música Rock
- Reproductor con 4 pistas generadas sintéticamente: Riff de la Victoria, Groove del Crecimiento, Balada Marchita (Rock), Semillas en Silencio.
- Se sugiere automáticamente la pista acorde a tu promedio actual.
- Puedes cambiar entre pistas libremente.

### Juegos
- **Trivia Rockera**: 8 preguntas aleatorias de todas las materias. Puntaje +10 por acierto. Feedback inmediato.
- **Memorama Natural**: Encuentra parejas de íconos (naturaleza + rock). Cuenta intentos y parejas. Al completar muestra tu resultado.

### Notificaciones
- Icono de campana en la barra superior con **contador de no leídas** (parpadea si hay nuevas).
- Pulsa la campana para abrir el centro de notificaciones.
- Puedes marcar como leídas individualmente, marcar todas, eliminar una o eliminar todas.
- Al recibir una nueva nota o inasistencia, aparece un **toast** flotante automático.

## 2. ROL: PROFESOR

### Panel docente
- Resumen: estudiantes, notas registradas, inasistencias, avisos sin leer.
- Accesos rápidos a registrar notas e inasistencias.
- **Importante**: El profesor **NO tiene acceso al jardín visual** del estudiante (privado del estudiante).

### Registrar notas (🎚️)
- Selecciona estudiante y materia.
- Elige período (Periodo 1–4) y observación opcional.
- **Barra deslizante animada**: arrastra el control o usa botones ±0.1 / ±0.5. El número grande y la barra **avanzan visualmente** hasta el valor.
- Al guardar, la nota se registra y el **estudiante recibe notificación automática**.
- Muestra últimas notas registradas por ti.

### Inasistencias (📋)
- Registra por estudiante, materia, fecha, tipo (ausencia/tardanza/excusa).
- Marca si está justificada.
- Observación opcional.
- Al guardar, envía **notificación automática** al estudiante.

### Estudiantes
- Listado completo con foto, curso, código, promedio e inasistencias.
- Vista de datos (no incluye jardín visual).

### Notificaciones
- Mismo centro de avisos que otros roles.

## 3. ROL: ADMINISTRADOR

### Panel general
- Métricas: total usuarios, estudiantes, profesores, promedio global.
- Auditoría de accesos (sesiones activas/cerradas, IP, hora).

### Usuarios
- **Crear usuario**: nombre, apellido, usuario, correo, contraseña, rol (Administrador/Profesor/Estudiante).
- Si es Estudiante: solicita curso. Si es Profesor: solicita especialidad.
- Se generan códigos automáticos (EST-YYYY-XXX / DOC-YYYY-XXX).
- Listado con filtros por rol, activar/desactivar, eliminar (con cascada de datos relacionados).

### Materias
- CRUD completo: nombre, código, área, color de placa, ícono (emoji), descripción.
- El **color e ícono** determinan la apariencia de las placas metálicas del estudiante.
- Activar/desactivar materias.

### Reportes
- **Promedio por materia**: total de notas, promedio, mínimo, máximo + barra visual.
- **Estudiantes en riesgo** (promedio < 3.0): listado para seguimiento.
- **Ranking de inasistencias**: totales y justificadas.

### Notificaciones
- Acceso al centro de avisos.

## 4. Recomendaciones de uso

- **Navegación**: Usa el menú lateral (se adapta a móvil con botón ☰).
- **Notificaciones**: Revisa la campana periódicamente; el sistema actualiza cada 8 segundos automáticamente.
- **Fotos**: Usa imágenes cuadradas para mejor visualización en el avatar circular.
- **Java Desktop**: Úsalo para generar reportes rápidos desde consola sin abrir el navegador (compartiendo la misma BD).
- **Seguridad**: No compartas contraseñas. Las contraseñas se almacenan con **bcrypt** (hash seguro).

## 5. Solución de problemas

| Problema | Causa probable | Solución |
|---|---|---|
| No carga `http://localhost/NaturalNoteRock/` | Apache no está activo | Iniciar Apache en XAMPP |
| Error de conexión a BD | MySQL no iniciado o credenciales distintas | Iniciar MySQL; verificar `database/db.properties` |
| Fotos no se suben | Tamaño > 2MB o formato no permitido | Usar JPG/PNG/GIF/WEBP < 2MB |
| Caracteres con tilde/acentos raros | Codificación | BD importada con `utf8mb4` (usar el comando con `--default-character-set=utf8mb4`) |
| Java no conecta | Falta conector JDBC | Verificar `java/lib/mysql-connector-j-8.2.0.jar` existe; revisar ruta en classpath |

## 6. Datos de demostración

El sistema incluye datos de prueba listos para explorar todas las funcionalidades (notas con distintos rangos para ver los 3 estados del jardín). Ver credenciales en README.md.
