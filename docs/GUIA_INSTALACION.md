# GUÍA DE INSTALACIÓN, EJECUCIÓN Y USO

## 1. Requisitos

- **XAMPP** (recomendado): Apache 2.4+, PHP 8.2+, MariaDB/MySQL 10.4+
- **Java JDK 8+** para compilar y ejecutar la aplicación desktop
- **Navegador moderno** (Chrome, Edge, Firefox) para la aplicación web

## 2. Instalación

### Paso 1: Copiar el proyecto a htdocs
Coloca la carpeta `NaturalNoteRock` completa dentro de `C:\xampp\htdocs\`.

Debe quedar así:
```
C:\xampp\htdocs\NaturalNoteRock\
  ├── index.php
  ├── login.php
  ├── ...
  └── java/
```

### Paso 2: Iniciar XAMPP
Abre XAMPP Control Panel y activa:
- Apache
- MySQL

### Paso 3: Importar la base de datos
Puedes hacerlo de dos formas:

**A) Desde línea de comandos**
```bat
cd C:\xampp\htdocs\NaturalNoteRock
"C:\xampp\mysql\bin\mysql.exe" -u root --default-character-set=utf8mb4 < database\natural_note_rock.sql
```

**B) Desde phpMyAdmin**
1. Ir a `http://localhost/phpMyAdmin`
2. Clic en "Nueva" → crear base de datos `natural_note_rock` con cotejamiento `utf8mb4_unicode_ci`
3. Seleccionarla → pestaña "Importar" → seleccionar `database/natural_note_rock.sql` → Importar

### Paso 4: Verificar configuración compartida
El archivo `database/db.properties` es **compartido** por PHP y Java. Por defecto:

```properties
db.host=127.0.0.1
db.port=3306
db.name=natural_note_rock
db.user=root
db.password=
db.charset=utf8mb4
```

Si tu usuario MySQL tiene contraseña, actualízala aquí (tanto PHP como Java la leerán).

## 3. Ejecución de la aplicación WEB

1. Asegura Apache y MySQL corriendo en XAMPP.
2. Abre tu navegador en: `http://localhost/NaturalNoteRock/`
3. Serás redirigido al login. Usa una de las cuentas de prueba (ver README.md).

## 4. Ejecución de la aplicación JAVA (Desktop)

La app Java **conecta a la misma base de datos** que el sitio web.

### Opción A: Ejecutar con script (recomendado)
Desde la carpeta `java/` hay un conector incluido en `lib/`. Para compilar/ejecutar:

**Compilar:**
```bat
cd C:\xampp\htdocs\NaturalNoteRock\java
mkdir build 2>NUL
javac -d build -sourcepath src -source 8 -target 8 src\uco\naturalnoterock\Main.java
```

**Ejecutar:**
```bat
cd C:\xampp\htdocs\NaturalNoteRock\java
java -cp build;lib\mysql-connector-j-8.2.0.jar uco.naturalnoterock.Main
```

Pulsa `s` para generar el **reporte académico** mostrando estudiantes, promedios, estado del "jardín" y notas por materia.

## 5. Uso del sistema web

### Rol: Administrador (`admin/admin123`)
- Panel general con métricas globales
- Gestión de usuarios (crear/activar/desactivar/eliminar)
- Gestión de materias (colores, íconos, códigos)
- Reportes institucionales (promedios por materia, estudiantes en riesgo, ranking de inasistencias)

### Rol: Profesor (`profe.roca/profe123`)
- Dashboard docente (sin acceso al jardín visual del estudiante)
- **Registrar notas** con barra deslizante animada (1.0–5.0). Al guardar, se envía notificación automática al estudiante.
- **Registrar inasistencias** (ausencia/tardanza/excusa, justificada/no). Notifica automáticamente.
- Listado de estudiantes con promedios e inasistencias

### Rol: Estudiante (`luna.roca/estudiante123`)
- **Mi jardín**: foto de perfil + placas metálicas por materia (color/ícono según materia)
- **Vista por materia (Jardín Vivo)**: nota numérica + jardín animado + música rockera según rendimiento
- **Mi perfil**: edición de datos + subida de foto
- **Aprendizaje**: recursos reales por materia (Khan Academy, Colombia Aprende, RAE, SENA, etc.)
- **Música rock**: reproductor con pistas generadas en vivo (Web Audio API)
- **Juegos**: Trivia Rockera + Memorama
- **Notificaciones**: contador sin leer, marcar como leídas/eliminar

## 6. Notas importantes

- **Fotos de perfil** se guardan en `uploads/avatares/`. Se crea con nombre único. Soporta JPG/PNG/GIF/WEBP, máx 2MB.
- **Protecciones**: `.htaccess` bloquea acceso directo a carpetas internas (`config/`, `src/`, `database/`, `includes/`, `docs/`). `uploads/.htaccess` evita ejecución de archivos PHP.
- **Conexión compartida**: un único `database/db.properties` alimenta PHP y Java. Cambiar credenciales ahí afecta a ambos.
- **Java**: el conector `mysql-connector-j-8.2.0.jar` ya viene en `java/lib/`.
