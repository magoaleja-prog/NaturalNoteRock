package uco.naturalnoterock.modelo;

/**
 * Profesor - hereda de Usuario (HERENCIA) con su propia descripcion
 * (POLIMORFISMO).
 */
public class Profesor extends Usuario {

    private String codigo;
    private String especialidad;

    public Profesor(int id, String nombre, String apellido, String username, String email,
                    String codigo, String especialidad) {
        super(id, nombre, apellido, username, email, "profesor");
        this.codigo = codigo;
        this.especialidad = especialidad;
    }

    @Override
    public String descripcion() {
        return "Profesor de " + especialidad + " (codigo " + codigo + ")";
    }

    public String getCodigo() { return codigo; }
    public String getEspecialidad() { return especialidad; }
}
