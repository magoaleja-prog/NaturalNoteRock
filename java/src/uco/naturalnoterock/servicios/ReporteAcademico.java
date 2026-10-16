package uco.naturalnoterock.servicios;

import uco.naturalnoterock.dao.EstudianteDAO;
import uco.naturalnoterock.dao.NotaDAO;
import uco.naturalnoterock.modelo.Estudiante;
import uco.naturalnoterock.modelo.Nota;

import java.sql.SQLException;
import java.util.List;

/**
 * ReporteAcademico - genera un reporte de vista institucional.
 * Demuestra ABSTRACCION (encapsula la logica de reportes) y separa
 * capas (servicio) de los DAOs.
 */
public class ReporteAcademico {

    private final EstudianteDAO estudianteDAO = new EstudianteDAO();
    private final NotaDAO notaDAO = new NotaDAO();

    /** Imprime un reporte completo por consola. */
    public void imprimirReporte() throws SQLException {
        System.out.println("=========================================");
        System.out.println("  NATURAL NOTE ROCK - REPORTE ACADEMICO");
        System.out.println("  Organizacion Educativa UCO");
        System.out.println("=========================================");
        System.out.println();
        System.out.println("1. PROMEDIO INSTITUCIONAL POR MATERIA");
        System.out.println("-----------------------------------------");
        System.out.printf("%-20s %-10s %-10s%n", "Materia", "Notas", "Promedio");
        for (String[] f : notaDAO.promedioPorMateria()) {
            System.out.printf("%-20s %-10s %-10s%n", f[0], f[1], f[2]);
        }
        System.out.println();
        System.out.println("2. ESTUDIANTES CON SU JARDIN ACADEMICO");
        System.out.println("-----------------------------------------");
        for (Estudiante e : estudianteDAO.listar()) {
            System.out.printf("%-4s %-22s %-12s %.1f  %s%n",
                    e.getCodigo(),
                    e.getNombreCompleto(),
                    e.getCurso(),
                    e.getPromedio(),
                    e.estadoJardin());
            List<Nota> notas = notaDAO.porEstudiante(e.getEstudianteId());
            if (!notas.isEmpty()) {
                for (Nota n : notas) {
                    System.out.printf("     · %-16s %-10s %.1f%n",
                            n.getMateria(), n.getPeriodo(), n.getValor());
                }
            }
            System.out.println();
        }
        System.out.println("=========================================");
        System.out.println("  Conexion compartida: PHP + Java");
        System.out.println("  (4 pilares de POO aplicados)");
        System.out.println("=========================================");
    }
}
