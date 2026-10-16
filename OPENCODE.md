# OPENCODE

Este documento describe **OpenCode**, la herramienta de asistencia con IA que se
utilizó como apoyo para construir el proyecto **Natural Note Rock**, y resume
cómo se trabajó con ella.

> El detalle de las instrucciones (prompts) usadas durante el desarrollo está en
> [`PROMPTS.md`](PROMPTS.md).

## ¿Qué es OpenCode?

**OpenCode** es un agente de programación (AI coding agent) que se ejecuta en la
terminal. Se conecta a un proveedor de modelos de lenguaje y puede **leer,
escribir y ejecutar código** dentro del proyecto mediante herramientas
(herramientas de archivos, búsqueda, shell, etc.).

Usa una arquitectura **cliente–servidor**: la interfaz (CLI/TUI) se conecta a un
servicio en segundo plano que gestiona las sesiones, la configuración, los
plugins y la ejecución de herramientas.

- Documentación oficial (V2): <https://opencode.ai/v2/docs/>
- Esquema de configuración: <https://opencode.ai/config.json>

## Configuración del proyecto

Este repositorio incluye el archivo [`opencode.json`](opencode.json) en la raíz:
es la **configuración de OpenCode para el proyecto** (qué instrucciones cargar,
qué archivos ignorar y una regla de permisos para `git push`).

```json
{
  "$schema": "https://opencode.ai/config.json",
  "instructions": ["README.md", "docs/ARQUITECTURA.md", "docs/PILARES_POO.md", "docs/MANUAL_USUARIO.md"]
}
```

- `$schema`: permite validación y autocompletado en el editor.
- `instructions`: documentos que OpenCode carga como contexto del proyecto.
- `watcher.ignore`: carpetas que no generan avisos de cambios (fotos subidas,
  compilados de Java, logs).
- `permissions`: pide confirmación antes de ejecutar `git push`.

## ¿Qué modelo se usó?

| Campo | Valor |
|---|---|
| Proveedor | `opencode` |
| Modelo | **Big Pickle** (`big-pickle`) |
| Modo | Agente de código (Code Mode) con acceso a herramientas |

## ¿Cómo se usó OpenCode en este proyecto?

OpenCode se empleó como **asistente de desarrollo** para:

1. **Analizar los requisitos** del proyecto (roles, materias, funcionalidades).
2. **Diseñar la base de datos** MySQL: 8 tablas
   (`Usuarios`, `Estudiantes`, `Profesores`, `Materias`, `Notas`,
   `Inasistencias`, `Sesiones`, `Notificaciones`) y el archivo
   `database/natural_note_rock.sql` con datos de demostración.
3. **Escribir la capa PHP orientada a objetos** aplicando los 4 pilares:
   - Abstracción: `Model` (abstracta), `Usuario` (abstracta), interfaces
     `Autenticable` y `Notificable`.
   - Herencia: `Usuario` → `Administrador`, `Profesor`, `Estudiante`.
   - Polimorfismo: `menu()`, `panelInicio()`, `permisos()`, `descripcionRol()`.
   - Encapsulamiento: Singleton `Database`, atributos privados con getters/setters.
4. **Construir la interfaz web** (HTML/CSS/JS) con estética *rock + naturaleza*:
   placas metálicas, jardín vivo en canvas, música rock sintetizada con la
   *Web Audio API*, barra deslizante animada para registrar notas, juegos y
   notificaciones en tiempo real.
5. **Crear la aplicación de escritorio en Java** que comparte la **misma base de
   datos** (y el mismo `database/db.properties`) que el sitio PHP, vía JDBC.
6. **Probar** el sistema: servidor embebido `php -S`, Apache de XAMPP, flujos de
   login por rol, control de acceso, subida de fotos y la API de notificaciones.
7. **Versionar** el trabajo con Git: ramas `master`/`develop`, ramas de
   funcionalidad y `hotfix`, con mensajes de commit descriptivos.
8. **Documentar**: guías de instalación y Git, manual de usuario, arquitectura y
   explicación de los pilares de la POO.

## Herramientas de OpenCode utilizadas

| Herramienta | Uso en el proyecto |
|---|---|
| Lectura de archivos | Revisar y entender el código existente |
| Escritura / edición | Crear y modificar archivos del proyecto |
| Búsqueda (glob/grep) | Localizar archivos y símbolos |
| Shell | Ejecutar PHP, MySQL, Java, Git y pruebas HTTP |
| Web | Consultar documentación de referencia y recursos |

## Comandos útiles de OpenCode

```bash
# Iniciar OpenCode en el proyecto (desde la carpeta del proyecto)
opencode

# Ver el estado del servicio en segundo plano
opencode service status

# Reiniciar el servicio si se queda trabado
opencode service restart

# Llamar a la API local de OpenCode
opencode api get /api/info
```

## Ubicación de los datos de OpenCode (local)

No forman parte del repositorio; viven en el equipo del usuario:

| Elemento | Ruta (Windows) |
|---|---|
| Configuración global | `%USERPROFILE%\.config\opencode\` |
| Datos / sesiones | `%USERPROFILE%\.local\share\opencode\` |
| Registro (log) | `%USERPROFILE%\.local\share\opencode\log\opencode.log` |

> Nota: las credenciales del servicio se guardan fuera del proyecto y **nunca**
> se incluyen en el control de versiones.
