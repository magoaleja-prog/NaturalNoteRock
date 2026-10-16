# PROMPTS UTILIZADOS

Registro de las instrucciones (prompts) que se le dieron a **OpenCode** durante
el desarrollo del proyecto **Natural Note Rock**.

> **Nota metodológica:** las respuestas del asistente no se transcriben aquí
> (son extensas y contienen el código generado). Solo se registran las
> **instrucciones del usuario** y un resumen de lo que produjo cada una.
> El primer prompt se reconstruye a partir del resumen de requisitos de la
> sesión; los siguientes se citan de forma literal.

---

## 1. Prompt inicial (brief del proyecto)

> Construir el proyecto educativo completo **"Natural Note Rock"** para la
> organización **UCO**, usando HTML, CSS, JavaScript, PHP, Java y MySQL, en
> `C:\xampp\htdocs\NaturalNoteRock`, aplicando los 4 pilares de la POO, con
> estética **rock + naturaleza** y **login obligatorio**.
>
> **Requisitos:**
> - 4 pilares de la POO (encapsulamiento, herencia, polimorfismo, abstracción).
> - Tecnologías: HTML, CSS, JavaScript, PHP, Java, MySQL, todo en XAMPP `htdocs`.
> - Login obligatorio; roles **Administrador, Profesor, Estudiante** con permisos
>   exclusivos.
> - Buenas prácticas de Git: historial claro, ramas organizadas y mensajes de
>   commit descriptivos.
> - Estética **rock** (texturas metálicas, tipografías fuertes, colores oscuros)
>   fusionada con **naturaleza** (verdes, flores, tonos tierra, elementos orgánicos).
> - Tablas: `Usuarios`, `Estudiantes`, `Profesores`, `Materias`, `Notas`,
>   `Inasistencias`, `Sesiones`, `Notificaciones`.
> - 12 materias: Religión, Sociales, Matemáticas, Español, Filosofía, Tecnología,
>   Media Técnica, Química, Naturales, Política, Ética, Estadística.
> - Conexión a base de datos **compartida** entre PHP y Java.
> - Subida de foto del estudiante mostrada en la sesión; edición de datos
>   personales después del login.
> - Notificaciones en tiempo real con contador de no leídas, marcar como leídas
>   y eliminar.
> - **Profesor:** barra deslizante animada para las notas (sin teclear),
>   registro de inasistencias, notificaciones; **sin** acceso al jardín del
>   estudiante.
> - **Estudiante:** placas metálicas por materia, jardín vivo según la nota
>   (alta = florece, media = moderado, baja = marchito + plagas), nota numérica
>   visible y música rock según el desempeño.
> - Extras: sección de aprendizaje con recursos reales, reproductor de música con
>   listas y juegos educativos.
> - Entregables: estructura en `htdocs`, código comentado y funcional, archivo
>   SQL, guía de instalación/ejecución/Git y explicación de los 4 pilares de la POO.

**Resultado:** estructura completa del proyecto, capa PHP orientada a objetos,
interfaz por roles, API de notificaciones, assets (canvas del jardín, música con
*Web Audio API*, barra deslizante, juegos), aplicación Java con conexión JDBC
compartida, base de datos y documentación.

---

## 2. Prompts de seguimiento (literales)

### 2.1 `sigue`
Continuar con el trabajo pendiente: terminar la documentación (`docs/`).

### 2.2 `me dice no found`
Diagnóstico del error *Not Found* al abrir el sitio. Se verificó que el problema
era que Apache no estaba iniciado / la ruta de acceso y se dieron los pasos para
levantar XAMPP y probar la URL correcta.

### 2.3 `no tiene nada de diseño y si doy clic o cualquier comando la ruta se pega`
Diagnóstico del **bug real**: `BASE_PATH` se calculaba **sin la barra final**
(`/NaturalNoteRock`), por lo que:
- el CSS se pedía a `/NaturalNoteRockassets/css/style.css` → 404 (sin diseño);
- los enlaces quedaban como `/NaturalNoteRocklogin.php` → 404 ("la ruta se pega").

**Corrección:** se normalizó `BASE_PATH` para que siempre termine en `/`
(commit `fix(rutas)`), se ajustó `.htaccess` (carpeta `includes/`) y se
reimportó la base de datos a estado limpio.

### 2.4 `antes de que sigas no hiciste ni un commit`
Reconstrucción del repositorio Git con **ramas organizadas** (`master`,
`develop`, `feature/jardin-vivo-musica`, `hotfix/rutas-base`) y **commits
descriptivos** por fase de desarrollo.

### 2.5 `puedes incluirme porfa el archivo opencode y otro de los prompts y subir esto a github muchas gracias`
- Se agregó [`opencode.json`](opencode.json) (configuración de OpenCode).
- Se agregó [`OPENCODE.md`](OPENCODE.md) (qué es y cómo se usó OpenCode).
- Se agregó este archivo [`PROMPTS.md`](PROMPTS.md) con el registro de prompts.
- Preparación del repositorio para **subirlo a GitHub**.

---

## 3. Resumen de respuestas del asistente

| # | Prompt | Acción / respuesta |
|---|---|---|
| 1 | Brief del proyecto | Se generó el proyecto completo (PHP+Java+MySQL+HTML/CSS/JS), la BD y la documentación. |
| 2 | `sigue` | Se completaron las guías y el manual; se prepararon los commits. |
| 3 | `me dice no found` | Se explicó cómo iniciar Apache/MySQL y la URL correcta. |
| 4 | Problema de diseño y rutas | Se corrigió el cálculo de `BASE_PATH` (commit `fix(rutas)`) y se verificó con Apache. |
| 5 | Faltaban commits | Se reestructuró el historial de Git con ramas y commits descriptivos. |
| 6 | Incluir opencode/prompts y subir a GitHub | Se crearon `opencode.json`, `OPENCODE.md` y `PROMPTS.md`; listo para publicar. |

---

## 4. Buenas prácticas al usar OpenCode

- Revisar y entender todo el código generado antes de entregarlo.
- Probar los flujos reales (login, roles, subida de fotos, notificaciones).
- Confirmar la codificación UTF-8, sobre todo al importar el SQL.
- Usar el control de versiones con mensajes claros para cada cambio.
- No versionar secretos ni datos personales (fotos subidas, credenciales).
