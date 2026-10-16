# EXPLICACIÓN DE LOS 4 PILARES DE LA POO

El proyecto **Natural Note Rock** aplica **los 4 pilares fundamentales de la Programación Orientada a Objetos** tanto en **PHP (aplicación web)** como en **Java (aplicación desktop)**, demostrando la universalidad de estos principios.

## 1. ENCAPSULAMIENTO

> Ocultar los detalles internos y exponer únicamente lo necesario, protegiendo el estado de los objetos.

### En PHP

- **`App\Core\Database` (Singleton)**: Constructor `private`, instancia única vía `getInstancia()`, conexión PDO encapsulada (`private PDO $pdo`). Nadie puede instanciarla directamente.
- **`App\Models\Usuario`**: Atributos `protected` (`$nombre`, `$apellido`, `$passwordHash`). Acceso controlado mediante **getters/setters** (`getNombreCompleto()`, `getId()`, `guardarFoto()`, `actualizarDatos()`).
- **`$_SESSION`** solo se manipula a través de `App\Support\Auth` (fachada), evitando acceso disperso.
- Las validaciones y conversiones quedan dentro de los modelos, no en las vistas.

### En Java

- **`ConexionBD`** (Singleton): Constructor `private`, `synchronized getInstancia()`, `Connection` encapsulado. Solo se expone `getConexion()`.
- **Clases `modelo.Usuario`, `Estudiante`, `Profesor`**: Campos `private` con **getters públicos**. El estado no es modificable libremente.
- **DAO**: Encapsulan el acceso a SQL, ocultando detalles JDBC al resto de la aplicación.

**Conclusión**: Se protege la integridad de los datos y se reduce el acoplamiento.

## 2. HERENCIA

> Permite reutilizar código y especializar clases a partir de una clase base común.

### En PHP

- **`App\Models\Usuario` es `abstract`** (clase padre/base). Define propiedades y comportamientos comunes (autenticación, notificaciones, foto, acceso).
- **Subclases**: `Administrador`, `Profesor`, `Estudiante` **heredan** de `Usuario`.
- Cada hija añade datos propios:
  - `Estudiante`: hidratado desde tabla `estudiantes` (código, curso, acudiente...)
  - `Profesor`: desde `profesores` (especialidad, título, código)
  - `Administrador`: no tiene tabla hija, pero hereda toda la funcionalidad base
- Método `Usuario::desdeFila()` actúa como **Factory**: instancia la subclase correcta según el `rol`.

### En Java

- **`modelo.Usuario` es `abstract`**.
- **`Estudiante extends Usuario`**: añade `estudianteId`, `codigo`, `curso`, `promedio` y `estadoJardin()`.
- **`Profesor extends Usuario`**: añade `codigo`, `especialidad`.
- Reutilización total de campos y métodos comunes (`getNombreCompleto()`, `toString()`).

**Conclusión**: Evita duplicidad de código y modela la jerarquía real del dominio (Usuario → Estudiante/Profesor/Administrador).

## 3. POLIMORFISMO

> Un mismo método puede tener diferentes comportamientos según la clase que lo implemente (sobreescritura y enlace dinámico).

### En PHP

**Sobreescritura (override) de métodos abstractos:**

- `Usuario::panelInicio(): string` → abstracto. Cada subclase devuelve su ruta:
  - `Administrador` → `admin/dashboard.php`
  - `Profesor` → `profesor/dashboard.php`
  - `Estudiante` → `estudiante/dashboard.php`
- `Usuario::menu(): array` → abstracto. Cada rol genera su propio menú lateral (diferente para admin/profesor/estudiante).
- `Usuario::descripcionRol(): string` → abstracto. Descripción específica por rol.
- `Usuario::permisos(): array` → abstracto. Permisos exclusivos.
- `Autenticable::autenticar()` se implementa en la clase base (común) y puede especializarse si fuera necesario.

**Uso polimórfico en tiempo de ejecución:**

- `Auth::requerirLogin()` devuelve `?Usuario`. En realidad es una instancia de `Administrador|Profesor|Estudiante`.
- En vistas: `$usuario->menu()` llama al método de la **subclase concreta** (no el abstracto). Lo mismo para `$usuario->panelInicio()`.
- `Auth::iniciar(Usuario $usuario)` acepta la clase abstracta, pero trabaja con el objeto especializado.

Esto permite que el código sea **genérico y extensible**: agregar un nuevo rol solo requiere heredar de `Usuario` e implementar sus métodos abstractos.

### En Java

- **`Usuario` declara `public abstract String descripcion()`**.
- `Estudiante` y `Profesor` **sobreescriben** (`@Override`) ese método con comportamientos distintos.
- En `ReporteAcademico`, iteramos sobre `List<Estudiante>` pero trabajamos con conceptos polimórficos; también `toString()` se especializa.
- El polimorfismo permite tratar objetos concretos a través de su tipo base abstracto.

**Conclusión**: El sistema toma decisiones por tipo dinámico, logrando flexibilidad y mantenibilidad.

## 4. ABSTRACCIÓN

> Simplificar la complejidad: enfocarse en "qué hace" y ocultar "cómo lo hace".

### En PHP

- **Clases abstractas**: `App\Core\Model` define operaciones genéricas (`all()`, `find()`, `delete()`, `contar()`) sobre tablas, mientras obliga a las hijas a implementar `reglas(): array` (contrato). Esto abstrae el acceso a BD común.
- **Interfaces**: `Autenticable` define el contrato `autenticar()`, `getRol()`, `panelInicio()`, `permisos()`. `Notificable` define `notificar()`, `misNotificaciones()`, `notificacionesNoLeidas()`. Las clases cumplen el contrato sin revelar su implementación interna.
- **`App\Support\Auth` (Fachada)**: Abstrae la complejidad de sesiones PHP + auditoría en tabla `sesiones`. Las páginas solo llaman `Auth::requerirRol(...)` o `Auth::usuario()`.
- **`App\Support\Jardin`**: Traduce una nota (número) a un estado completo del jardín (etiquetas, música, colores, conteos). El resto del código no calcula umbrales, solo consume el resultado.
- **`App\Support\Contenido`**: Abstrae recursos de aprendizaje por código de materia (centraliza datos).
- **Vistas/Páginas**: Trabajan con objetos de alto nivel (`$usuario`, modelos), sin escribir SQL directo (excepto casos puntuales en reportes/admin con agregados, pero aún encapsulados).

### En Java

- **`Usuario` abstracta**: Define el contrato común y obliga a especializar comportamiento (`descripcion()`).
- **DAO (`EstudianteDAO`, `NotaDAO`)**: Abstraen toda la lógica JDBC (PreparedStatement, ResultSet). Quien consume el DAO solo recibe listas de objetos `Estudiante/Nota`.
- **`ReporteAcademico` (Servicio)**: Abstrae la generación del reporte completo. `Main` solo invoca `imprimirReporte()` sin conocer detalles de consultas o formato.
- **`ConexionBD`**: Oculta cómo se lee `db.properties`, cómo se carga el driver y cómo se obtiene la conexión.

**Conclusión**: Cada capa expone una API simple y coherente, reduciendo la complejidad total del sistema.

## Aplicación integrada

Los 4 pilares trabajan **juntos**:

1. **Abstracción** define contratos (interfaces/clases abstractas)
2. **Herencia** reutiliza y especializa
3. **Polimorfismo** permite comportamiento dinámico según el rol
4. **Encapsulamiento** protege y mantiene la coherencia del estado

Esta arquitectura hace que el proyecto sea **mantenible, extensible y fácil de entender**, cumpliendo con los requerimientos de buenas prácticas de POO solicitados.
