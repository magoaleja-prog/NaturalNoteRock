package uco.naturalnoterock.modelo;

/**
 * Nota - representa una calificacion (1.0 - 5.0).
 */
public class Nota {

    private int id;
    private String materia;
    private String periodo;
    private double valor;
    private String observacion;

    public Nota(int id, String materia, String periodo, double valor, String observacion) {
        this.id = id;
        this.materia = materia;
        this.periodo = periodo;
        this.valor = valor;
        this.observacion = observacion;
    }

    public int getId() { return id; }
    public String getMateria() { return materia; }
    public String getPeriodo() { return periodo; }
    public double getValor() { return valor; }
    public String getObservacion() { return observacion; }

    @Override
    public String toString() {
        return String.format("%-16s %-11s %.1f  %s",
                materia, periodo, valor, observacion == null ? "" : observacion);
    }
}
