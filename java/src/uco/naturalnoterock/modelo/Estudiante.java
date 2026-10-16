package uco.naturalnoterock.modelo;

/**
 * Estudiante - hereda de Usuario (HERENCIA) e implementa su propia
 * descripcion (POLIMORFISMO).
 */
public class Estudiante extends Usuario {

    private int estudianteId;
    private String codigo;
    private String curso;
    private double promedio;

    public Estudiante(int id, String nombre, String apellido, String username, String email,
                      int estudianteId, String codigo, String curso) {
        super(id, nombre, apellido, username, email, "estudiante");
        this.estudianteId = estudianteId;
        this.codigo = codigo;
        this.curso = curso;
        this.promedio = 0.0;
    }

    @Override
    public String descripcion() {
        return "Estudiante del curso " + curso + " (codigo " + codigo + ")";
    }

    public int getEstudianteId() { return estudianteId; }
    public String getCodigo() { return codigo; }
    public String getCurso() { return curso; }

    public double getPromedio() { return promedio; }
    public void setPromedio(double promedio) { this.promedio = promedio; }

    /** Estado del jardin segun el promedio (misma logica que en PHP). */
    public String estadoJardin() {
        if (promedio >= 4.0) return "🌸 Jardin florecido";
        if (promedio >= 3.0) return "🌿 Crecimiento moderado";
        if (promedio > 0)    return "🥀 Plantas marchitas y plagas";
        return "🌱 Sin notas aun";
    }
}
