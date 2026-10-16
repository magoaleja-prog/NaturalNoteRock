package uco.naturalnoterock;

import java.io.InputStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Properties;

/**
 * ConexionBD - Conexion COMPARTIDA a MySQL (patron Singleton).
 *
 * Lee los mismos parametros que usa PHP del archivo
 * database/db.properties, de modo que toda la aplicacion (PHP + Java)
 * trabaja sobre la MISMA base de datos natural_note_rock.
 *
 * PILAR POO - ENCAPSULAMIENTO: el constructor es privado y la conexion
 * queda oculta; solo se accede por getConexion().
 */
public final class ConexionBD {

    private static ConexionBD instancia;
    private final Connection conexion;

    /** Constructor privado: solo se crea desde getInstancia(). */
    private ConexionBD() {
        Properties props = cargarPropiedades();
        String host = props.getProperty("db.host", "127.0.0.1");
        String port = props.getProperty("db.port", "3306");
        String name = props.getProperty("db.name", "natural_note_rock");
        String user = props.getProperty("db.user", "root");
        String pass = props.getProperty("db.password", "");
        String charset = props.getProperty("db.charset", "utf8mb4");

        String url = "jdbc:mysql://" + host + ":" + port + "/" + name
                + "?useUnicode=true&characterEncoding=UTF-8"
                + "&serverTimezone=America/Bogota&useSSL=false";

        try {
            // El driver moderno se registra solo, pero forzamos la carga.
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            try {
                Class.forName("com.mysql.jdbc.Driver");
            } catch (ClassNotFoundException ignored) {
                throw new IllegalStateException(
                    "Falta el conector MySQL/J. Agrega mysql-connector-j al classpath.", ignored);
            }
        }

        try {
            this.conexion = DriverManager.getConnection(url, user, pass);
        } catch (SQLException e) {
            throw new IllegalStateException("No se pudo conectar a MySQL: " + e.getMessage(), e);
        }
    }

    /**
     * Busca database/db.properties subiendo desde el directorio actual.
     * Permite ejecutar el programa desde java/ o desde la raiz.
     */
    private Properties cargarPropiedades() {
        Properties props = new Properties();
        String[] candidatos = {
            "../database/db.properties",
            "database/db.properties",
            "db.properties"
        };
        for (String ruta : candidatos) {
            Path p = Paths.get(ruta);
            if (Files.isRegularFile(p)) {
                try (InputStream in = Files.newInputStream(p)) {
                    props.load(new java.io.InputStreamReader(in, "UTF-8"));
                    return props;
                } catch (Exception ignored) {
                    // Intentamos con el siguiente candidato.
                }
            }
        }
        System.out.println("[ConexionBD] Aviso: no se encontro db.properties; uso valores por defecto.");
        return props;
    }

    /** Punto de acceso unico a la conexion. */
    public static synchronized ConexionBD getInstancia() {
        if (instancia == null) {
            instancia = new ConexionBD();
        }
        return instancia;
    }

    /** Devuelve la conexion JDBC. */
    public Connection getConexion() {
        return conexion;
    }

    /** Cierra la conexion (al salir del programa). */
    public void cerrar() {
        try {
            if (conexion != null && !conexion.isClosed()) {
                conexion.close();
            }
        } catch (SQLException ignored) {
            // Nada que hacer al cerrar.
        }
    }
}
