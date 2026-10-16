package uco.naturalnoterock.dao;

import uco.naturalnoterock.ConexionBD;
import uco.naturalnoterock.modelo.Estudiante;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

/**
 * EstudianteDAO - acceso a datos de estudiantes (patron DAO).
 * Separa la logica de base de datos de los objetos de modelo.
 */
public class EstudianteDAO {

    private final Connection conexion = ConexionBD.getInstancia().getConexion();

    /** Lista todos los estudiantes con su promedio general. */
    public List<Estudiante> listar() throws SQLException {
        String sql =
            "SELECT u.id, u.nombre, u.apellido, u.username, u.email, " +
            "       e.id AS estudiante_id, e.codigo, e.curso, " +
            "       ROUND(AVG(n.valor),1) AS promedio " +
            "  FROM estudiantes e " +
            "  JOIN usuarios u ON u.id = e.usuario_id " +
            "  LEFT JOIN notas n ON n.estudiante_id = e.id " +
            " GROUP BY e.id " +
            " ORDER BY e.curso, u.apellido";

        List<Estudiante> lista = new ArrayList<Estudiante>();
        try (PreparedStatement ps = conexion.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                Estudiante est = new Estudiante(
                        rs.getInt("id"),
                        rs.getString("nombre"),
                        rs.getString("apellido"),
                        rs.getString("username"),
                        rs.getString("email"),
                        rs.getInt("estudiante_id"),
                        rs.getString("codigo"),
                        rs.getString("curso"));
                est.setPromedio(rs.getDouble("promedio"));
                lista.add(est);
            }
        }
        return lista;
    }

    /** Busca un estudiante por su id de estudiante. */
    public Estudiante buscarPorId(int estudianteId) throws SQLException {
        String sql =
            "SELECT u.id, u.nombre, u.apellido, u.username, u.email, " +
            "       e.id AS estudiante_id, e.codigo, e.curso, " +
            "       (SELECT ROUND(AVG(valor),1) FROM notas WHERE estudiante_id = e.id) AS promedio " +
            "  FROM estudiantes e " +
            "  JOIN usuarios u ON u.id = e.usuario_id " +
            " WHERE e.id = ?";
        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setInt(1, estudianteId);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    Estudiante est = new Estudiante(
                            rs.getInt("id"),
                            rs.getString("nombre"),
                            rs.getString("apellido"),
                            rs.getString("username"),
                            rs.getString("email"),
                            rs.getInt("estudiante_id"),
                            rs.getString("codigo"),
                            rs.getString("curso"));
                    est.setPromedio(rs.getDouble("promedio"));
                    return est;
                }
            }
        }
        return null;
    }
}
