package uco.naturalnoterock.modelo;

/**
 * Usuario - clase ABSTRACTA base (espejo en Java del modelo PHP).
 *
 * PILARES POO:
 *   - ENCAPSULAMIENTO: atributos privados con getters/setters.
 *   - HERENCIA: Estudiante y Profesor extienden de aqui.
 *   - POLIMORFISMO: rol() y descripcion() se comportan distinto.
 *   - ABSTRACCION: no se puede instanciar directamente.
 */
public abstract class Usuario {

    private int id;
    private String nombre;
    private String apellido;
    private String username;
    private String email;
    private String rol;

    public Usuario(int id, String nombre, String apellido, String username, String email, String rol) {
        this.id = id;
        this.nombre = nombre;
        this.apellido = apellido;
        this.username = username;
        this.email = email;
        this.rol = rol;
    }

    // --- Getters / Setters (encapsulamiento) ---
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getNombre() { return nombre; }
    public void setNombre(String nombre) { this.nombre = nombre; }

    public String getApellido() { return apellido; }
    public void setApellido(String apellido) { this.apellido = apellido; }

    public String getUsername() { return username; }
    public String getEmail() { return email; }
    public String getRol() { return rol; }

    public String getNombreCompleto() {
        return nombre + " " + apellido;
    }

    // --- Metodos abstractos (polimorfismo) ---
    /** Descripcion del rol para mostrar en consola. */
    public abstract String descripcion();

    @Override
    public String toString() {
        return String.format("#%d %s (%s)", id, getNombreCompleto(), rol);
    }
}
