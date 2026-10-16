package uco.naturalnoterock.dao;

import uco.naturalnoterock.ConexionBD;
import uco.naturalnoterock.modelo.Nota;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

/**
 * NotaDAO - consultas de notas por estudiante.
 */
public class NotaDAO {

    private final Connection conexion = ConexionBD.getInstancia().getConexion();

    /** Notas de un estudiante con el nombre de la materia. */
    public List<Nota> porEstudiante(int estudianteId) throws SQLException {
        String sql =
            "SELECT n.id, m.nombre AS materia, n.periodo, n.valor, n.observacion " +
            "  FROM notas n JOIN materias m ON m.id = n.materia_id " +
            " WHERE n.estudiante_id = ? " +
            " ORDER BY m.nombre";

        List<Nota> lista = new ArrayList<Nota>();
        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setInt(1, estudianteId);
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    lista.add(new Nota(
                            rs.getInt("id"),
                            rs.getString("materia"),
                            rs.getString("periodo"),
                            rs.getDouble("valor"),
                            rs.getString("observacion")));
                }
            }
        }
        return lista;
    }

    /** Materias con su promedio institucional. */
    public List<String[]> promedioPorMateria() throws SQLException {
        String sql =
            "SELECT m.nombre, COUNT(n.id) AS total, ROUND(AVG(n.valor),2) AS promedio " +
            "  FROM materias m LEFT JOIN notas n ON n.materia_id = m.id " +
            " GROUP BY m.id ORDER BY promedio DESC";
        List<String[]> filas = new ArrayList<String[]>();
        try (PreparedStatement ps = conexion.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                filas.add(new String[]{
                        rs.getString("nombre"),
                        String.valueOf(rs.getInt("total")),
                        rs.getObject("promedio") == null ? "—" : String.format("%.2f", rs.getDouble("promedio"))
                });
            }
        }
        return filas;
    }
}
