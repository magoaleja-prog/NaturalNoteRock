<?php
/**
 * src/Core/Model.php
 * ---------------------------------------------------------------------
 * Clase ABSTRACTA base para todos los modelos (tablas) del sistema.
 *
 * PILAR POO - ABSTRACCION:
 *   Define el "contrato" comun (all, find, insert, update, delete)
 *   sin importar la tabla concreta. El resto del codigo trabaja con
 *   Model sin conocer los detalles de SQL.
 *
 * PILAR POO - ENCAPSULAMIENTO:
 *   $db y $tabla son protected; el exterior no toca la conexion.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Core;

use App\Core\Database;
use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $tabla = '';

    public function __construct()
    {
        $this->db = Database::getInstancia()->getConexion();
    }

    /**
     * Cada modelo concreto debe declarar sus reglas de validacion.
     * Es abstracto -> obliga a las hijas a implementarlo (ABSTRACCION).
     *
     * @return array<string,string> campo => mensaje de error
     */
    abstract public function reglas(): array;

    /** Devuelve todos los registros de la tabla. */
    public function all(string $orden = 'id ASC'): array
    {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY {$orden}";
        return $this->db->query($sql)->fetchAll();
    }

    /** Busca un registro por su llave primaria. */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabla} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Elimina un registro por id. */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->tabla} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /** Cuenta el total de registros de la tabla. */
    public function contar(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->tabla}")->fetchColumn();
    }

    /** Devuelve la conexion PDO (util para consultas personalizadas). */
    public function conexion(): PDO
    {
        return $this->db;
    }
}
