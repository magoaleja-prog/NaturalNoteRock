<?php
/**
 * config/Database.php
 * ---------------------------------------------------------------------
 * Conexion COMPARTIDA a MySQL mediante el patron SINGLETON.
 *
 * Toda la aplicacion (PHP) y el programa Java leen los mismos
 * parametros y apuntan a la MISMA base de datos `natural_note_rock`.
 *
 * PILAR POO - ENCAPSULAMIENTO:
 *   - El constructor es privado: nadie puede hacer `new Database()`.
 *   - La unica instancia se obtiene con getInstancia().
 *   - El PDO interno queda protegido; solo se expone via getConexion().
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    /** @var Database|null Instancia unica (Singleton). */
    private static ?Database $instancia = null;

    /** @var PDO Conexion activa, encapsulada. */
    private PDO $pdo;

    /**
     * Constructor privado: solo se invoca desde getInstancia().
     */
    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'No fue posible conectar a la base de datos: ' . $e->getMessage()
            );
        }
    }

    /** Impide clonar la instancia. */
    private function __clone()
    {
    }

    /** Impide deserializar otra instancia. */
    public function __wakeup()
    {
        throw new RuntimeException('No se permite deserializar el Singleton.');
    }

    /**
     * Punto de acceso unico a la conexion.
     */
    public static function getInstancia(): Database
    {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    /** Devuelve el objeto PDO compartido. */
    public function getConexion(): PDO
    {
        return $this->pdo;
    }
}
