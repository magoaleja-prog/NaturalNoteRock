<?php
/**
 * src/Models/Usuario.php
 * ---------------------------------------------------------------------
 * Clase ABSTRACTA que representa a cualquier persona del sistema.
 *
 * PILARES APLICADOS:
 *   - ENCAPSULAMIENTO: atributos protected + getters/setters controlados.
 *   - HERENCIA: Administrador, Profesor y Estudiante heredan de aqui.
 *   - POLIMORFISMO: los metodos abstractos (panelInicio, permisos, menu,
 *     descripcionRol) se comportan distinto en cada subclase.
 *   - ABSTRACCION: implementa las interfaces Autenticable y Notificable;
 *     expone "que hace" un usuario sin revelar "como".
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;
use App\Interfaces\Autenticable;
use App\Interfaces\Notificable;
use PDO;

abstract class Usuario extends Model implements Autenticable, Notificable
{
    protected string $tabla = 'usuarios';

    // --- Estado encapsulado -----------------------------------------
    protected int    $id = 0;
    protected string $nombre = '';
    protected string $apellido = '';
    protected string $username = '';
    protected string $email = '';
    protected string $passwordHash = '';
    protected string $rol = '';
    protected ?string $foto = null;
    protected bool   $activo = true;

    /**
     * Fabrica (FACTORY): recibe una fila de la BD y devuelve la subclase
     * correcta. Aqui brilla la abstraccion: quien llama no sabe cual es.
     */
    public static function desdeFila(array $fila): Usuario
    {
        $usuario = match ($fila['rol']) {
            'administrador' => new Administrador(),
            'profesor'      => new Profesor(),
            default         => new Estudiante(),
        };

        $usuario->id           = (int) $fila['id'];
        $usuario->nombre       = $fila['nombre'];
        $usuario->apellido     = $fila['apellido'];
        $usuario->username     = $fila['username'];
        $usuario->email        = $fila['email'];
        $usuario->passwordHash = $fila['password_hash'];
        $usuario->rol          = $fila['rol'];
        $usuario->foto         = $fila['foto'] ?: null;
        $usuario->activo       = (bool) $fila['activo'];

        $usuario->hidratarExtra();
        return $usuario;
    }

    /** Cada subclase carga sus datos propios (de su tabla hija). */
    protected function hidratarExtra(): void
    {
    }

    /** Busca un usuario por id y devuelve su objeto polimorfico. */
    public static function buscarPorId(int $id): ?Usuario
    {
        $db = Database::getInstancia()->getConexion();
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = :id AND activo = 1 LIMIT 1');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ? self::desdeFila($fila) : null;
    }

    /** Busca por username o email (para el login). */
    public static function buscarPorCredencial(string $credencial): ?Usuario
    {
        $db = Database::getInstancia()->getConexion();
        $stmt = $db->prepare(
            'SELECT * FROM usuarios
             WHERE (username = :u OR email = :e) AND activo = 1 LIMIT 1'
        );
        $stmt->execute([':u' => $credencial, ':e' => $credencial]);
        $fila = $stmt->fetch();
        return $fila ? self::desdeFila($fila) : null;
    }

    // --- Implementacion de Autenticable ------------------------------
    public function autenticar(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    // --- Implementacion de Notificable -------------------------------
    public function notificar(string $titulo, string $mensaje, string $tipo = 'sistema', ?string $url = null): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, url)
             VALUES (:u, :t, :m, :tp, :url)'
        );
        $stmt->execute([
            ':u' => $this->id, ':t' => $titulo, ':m' => $mensaje,
            ':tp' => $tipo, ':url' => $url,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function misNotificaciones(int $limite = 30): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notificaciones WHERE usuario_id = :u
             ORDER BY leida ASC, creado_en DESC LIMIT ' . max(1, $limite)
        );
        $stmt->execute([':u' => $this->id]);
        return $stmt->fetchAll();
    }

    public function notificacionesNoLeidas(): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM notificaciones WHERE usuario_id = :u AND leida = 0'
        );
        $stmt->execute([':u' => $this->id]);
        return (int) $stmt->fetchColumn();
    }

    // --- Metodos concretos compartidos -------------------------------
    public function marcarAcceso(): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }

    /** Guarda la ruta de la nueva foto de perfil. */
    public function guardarFoto(string $ruta): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET foto = :f WHERE id = :id');
        $stmt->execute([':f' => $ruta, ':id' => $this->id]);
        $this->foto = $ruta;
    }

    /** Actualiza datos personales editables. */
    public function actualizarDatos(array $datos): bool
    {
        $sql = 'UPDATE usuarios SET nombre = :n, apellido = :a, email = :e';
        $params = [
            ':n' => $datos['nombre'], ':a' => $datos['apellido'],
            ':e' => $datos['email'], ':id' => $this->id,
        ];
        if (!empty($datos['password'])) {
            $sql .= ', password_hash = :p';
            $params[':p'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }
        $sql .= ' WHERE id = :id';
        return $this->db->prepare($sql)->execute($params);
    }

    // --- Getters (encapsulamiento) -----------------------------------
    public function getId(): int { return $this->id; }
    public function getNombreCompleto(): string { return trim($this->nombre . ' ' . $this->apellido); }
    public function getNombre(): string { return $this->nombre; }
    public function getApellido(): string { return $this->apellido; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getFoto(): ?string { return $this->foto; }
    public function estaActivo(): bool { return $this->activo; }

    /** URL de la foto o un avatar generado por defecto. */
    public function getFotoUrl(): string
    {
        if ($this->foto && is_file(APP_ROOT . '/' . $this->foto)) {
            return url($this->foto) . '?v=' . @filemtime(APP_ROOT . '/' . $this->foto);
        }
        return asset('img/avatar_default.svg');
    }

    // --- Metodos ABSTRACTOS (polimorfismo puro) ----------------------
    /** Cada rol arma su propio menu lateral. */
    abstract public function menu(): array;

    /** Descripcion legible del rol (para el perfil). */
    abstract public function descripcionRol(): string;

    /** Reglas de validacion comunes. */
    public function reglas(): array
    {
        return [
            'nombre'   => 'El nombre es obligatorio.',
            'apellido' => 'El apellido es obligatorio.',
            'email'    => 'El correo es obligatorio.',
        ];
    }
}
