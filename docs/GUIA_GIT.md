# GUÍA DE GIT

## Buenas prácticas aplicadas
- **Historial claro**: commits atómicos con mensajes descriptivos.
- **Ramas organizadas**: rama `master` (estable). Para este proyecto se realizó desarrollo incremental con commits descriptivos.
- **Mensajes descriptivos**: cada commit explica qué se agrega o modifica (estructura, modelos, UI, Java, docs...).

## Flujo de trabajo recomendado

### 1. Clonar / trabajar con el repositorio
```bash
cd C:\xampp\htdocs
git clone <url-repo> NaturalNoteRock
cd NaturalNoteRock
```

### 2. Crear una rama de desarrollo
```bash
git checkout -b develop
```

### 3. Hacer cambios y commitear
```bash
git add .
git commit -m "feat: Agrega controlador de notas con barra deslizante animada"
```

### 4. Mantener ramas limpias
Antes de fusionar, actualizar `develop` con `master`:
```bash
git checkout develop
git pull origin master
git rebase origin/master  # o merge según política
```

### 5. Fusionar a master
```bash
git checkout master
git merge --no-ff develop -m "merge: Integra mejoras de interfaz del estudiante"
git push origin master
```

## Convenciones de commits

Se recomienda usar el siguiente formato:

| Prefijo | Uso |
|---|---|
| `feat:` | Nueva funcionalidad |
| `fix:` | Corrección de errores |
| `docs:` | Documentación |
| `style:` | Estilos, formato |
| `refactor:` | Refactorización sin cambiar comportamiento |
| `test:` | Pruebas |
| `chore:` | Tareas de mantenimiento |
| `db:` | Cambios en base de datos |

**Ejemplos:**
```bash
git commit -m "feat: Agrega jardín vivo con animación en canvas"
git commit -m "db: Agrega tabla notificaciones y seed inicial"
git commit -m "docs: Crea guía de instalación y pilar POO"
git commit -m "fix: Corrige codificación UTF-8 al importar SQL"
git commit -m "java: Agrega aplicación desktop con conexión compartida JDBC"
```

## Git inicial aplicado a este proyecto

El repositorio actual ya fue inicializado (`git init`) con un historial de desarrollo progresivo. Cada bloque funcional (estructura base, OOP PHP, UI web, API, Java, assets, documentación) fue registrado con commits descriptivos para garantizar **trazabilidad y buenas prácticas**.

## Ignorar archivos
Consulta `.gitignore` para ver qué archivos no se versionan (fotos de perfil subidas, logs, compilados Java, temporales).
