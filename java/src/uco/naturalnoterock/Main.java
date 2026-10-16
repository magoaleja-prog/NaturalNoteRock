package uco.naturalnoterock;

import uco.naturalnoterock.servicios.ReporteAcademico;

import java.sql.SQLException;
import java.util.Scanner;

/**
 * Main - Aplicacion de consola que demuestra la conexion COMPARTIDA con
 * la base de datos de Natural Note Rock.
 *
 * Muestra como en Java tambien se aplican los 4 pilares de la POO
 * (herencia, polimorfismo, abstraccion, encapsulamiento).
 */
public class Main {

    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        System.out.println("╔══════════════════════════════════════════════════════════════════╗");
        System.out.println("║        NATURAL NOTE ROCK - APLICACION DESKTOP (JAVA)              ║");
        System.out.println("║            Organizacion Educativa UCO · PHP + Java + MySQL       ║");
        System.out.println("╚══════════════════════════════════════════════════════════════════╝");

        try {
            System.out.println("\nConectando a la base de datos natural_note_rock...");
            // Fuerza la inicializacion del Singleton.
            ConexionBD.getInstancia().getConexion();
            System.out.println("Conexion exitosa. ¡Compartida con la app PHP!");
            System.out.println("\nPolimorfismo en Java: Usuario -> Estudiante | Profesor");
            System.out.println("Abstraccion: Usuario abstracta, Interface equivalente al OOP PHP");
            System.out.println("Encapsulamiento: atributos privados + getters");
            System.out.println("Herencia: Estudiante y Profesor heredan de Usuario");
            System.out.println();
            System.out.print("¿Generar reporte academico? (s/n): ");
            String resp = sc.nextLine().trim().toLowerCase();
            if (resp.equals("s") || resp.equals("si")) {
                new ReporteAcademico().imprimirReporte();
            } else {
                System.out.println("Reporte cancelado.");
            }
        } catch (SQLException e) {
            System.err.println("Error SQL: " + e.getMessage());
            System.err.println("Verifica que MySQL este corriendo y la BD exista.");
        } catch (Exception e) {
            System.err.println("Error de ejecucion: " + e.getMessage());
        } finally {
            ConexionBD.getInstancia().cerrar();
            sc.close();
        }
    }
}
