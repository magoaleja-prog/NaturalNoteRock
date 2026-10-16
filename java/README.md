# Natural Note Rock - Aplicacion Java

Esta aplicacion Java se conecta a la MISMA base de datos que la aplicacion PHP. Lee la configuracion desde `../database/db.properties`.

## Requisitos
- Java JDK 8+ (compila y corre con el JRE 1.8 disponible)
- MariaDB/MySQL en XAMPP
- mysql-connector-j (el conector JDBC de MySQL). En XAMPP suele estar en `C:/xampp/mysql/lib/` o puedes usar `mysql-connector-j-8.2.x.jar`.

## Estructura
```
java/
  src/uco/naturalnoterock/*.java
  src/uco/naturalnoterock/modelo/*.java
  src/uco/naturalnoterock/dao/*.java
  src/uco/naturalnoterock/servicios/*.java
```

## Compilar (PowerShell o CMD)
```bat
cd java
mkdir build 2>NUL
javac -d build -sourcepath src src\uco\naturalnoterock\Main.java
```

## Ejecutar
```bat
cd java
java -cp build;C:\xampp\mysql\lib\mysql-connector-j-8.2.0.jar uco.naturalnoterock.Main
```

El jar del conector puede tener otro numero de version. Ajustalo al archivo existente.

## ¿Que demuestra?
- **Singleton**: `ConexionBD` garantiza una unica conexion.
- **Herencia**: `Estudiante` y `Profesor` heredan de `Usuario` abstracta.
- **Polimorfismo**: cada subclase redefine `descripcion()` y aporta su estado.
- **Abstraccion**: `Usuario` no se instancia; el sistema trabaja con la clase abstracta.
- **Encapsulamiento**: campos privados con getters/setters.
- **DAO/Servicio**: separacion entre acceso a datos, logica de negocio y UI.

## Conexion compartida
PHP lee `database/db.properties`. Java lee el mismo archivo. Ambos apuntan a `natural_note_rock`. Así no hay dos conexiones distintas: **es la misma base de datos.**
