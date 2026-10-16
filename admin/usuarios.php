<?php
/**
 * admin/usuarios.php
 * ---------------------------------------------------------------------
 * CRUD de usuarios (solo administrador). Permite crear usuarios de
 * cualquier rol y, si es estudiante o profesor, crea su ficha hija
 * (relacion 1:1 de la herencia).
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['administrador'];
require_once __DIR__ . '/../includes/guard.php';

use App\Core\Database;

/** @var \App\Models\Administrador $usuario */
$db = Database::getInstancia()->getConexion();
$ok = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // ---------- Crear usuario ----------
    if ($accion === 'crear') {
        $nombre   = trim((string) ($_POST['nombre'] ?? ''));
        $apellido = trim((string) ($_POST['apellido'] ?? ''));
        $username = trim((string) ($_POST['username'] ?? ''));
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $rol      = (string) ($_POST['rol'] ?? 'estudiante');
        $extra    = trim((string) ($_POST['extra'] ?? '')); // curso o especialidad

        if ($nombre === '' || $apellido === '' || $username === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 5 ||
            !in_array($rol, ['administrador', 'profesor', 'estudiante'], true)) {
            $error = 'Revisa los datos. La contrasena debe tener al menos 5 caracteres.';
        } else {
            try {
                $db->beginTransaction();
                $stmt = $db->prepare(
                    'INSERT INTO usuarios (nombre, apellido, username, email, password_hash, rol)
                     VALUES (:n, :a, :u, :e, :p, :r)'
                );
                $stmt->execute([
                    ':n' => $nombre, ':a' => $apellido, ':u' => $username,
                    ':e' => $email, ':p' => password_hash($password, PASSWORD_DEFAULT),
                    ':r' => $rol,
                ]);
                $uid = (int) $db->lastInsertId();

                if ($rol === 'estudiante') {
                    $db->prepare(
                        'INSERT INTO estudiantes (usuario_id, codigo, curso)
                         VALUES (:u, :c, :cu)'
                    )->execute([':u' => $uid, ':c' => 'EST-' . date('Y') . '-' . str_pad((string) $uid, 3, '0', STR_PAD_LEFT), ':cu' => $extra ?: 'Sin curso']);
                } elseif ($rol === 'profesor') {
                    $db->prepare(
                        'INSERT INTO profesores (usuario_id, codigo, especialidad)
                         VALUES (:u, :c, :es)'
                    )->execute([':u' => $uid, ':c' => 'DOC-' . date('Y') . '-' . str_pad((string) $uid, 3, '0', STR_PAD_LEFT), ':es' => $extra ?: 'General']);
                }

                $db->commit();
                $ok = 'Usuario creado correctamente. 🎸';
            } catch (Throwable $ex) {
                $db->rollBack();
                $error = 'No se pudo crear: ' . $ex->getMessage();
            }
        }
    }

    // ---------- Activar / desactivar ----------
    if ($accion === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id !== $usuario->getId()) { // el admin no se desactiva a si mismo
            $db->prepare('UPDATE usuarios SET activo = 1 - activo WHERE id = :id')->execute([':id' => $id]);
            $ok = 'Estado actualizado.';
        } else {
            $error = 'No puedes desactivar tu propia cuenta.';
        }
    }

    // ---------- Eliminar ----------
    if ($accion === 'eliminar') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id !== $usuario->getId()) {
            $db->prepare('DELETE FROM usuarios WHERE id = :id')->execute([':id' => $id]);
            $ok = 'Usuario eliminado (y sus datos asociados por cascada).';
        } else {
            $error = 'No puedes eliminar tu propia cuenta.';
        }
    }
}

$usuarios = $db->query(
    'SELECT u.*, e.curso, p.especialidad
       FROM usuarios u
       LEFT JOIN estudiantes e ON e.usuario_id = u.id
       LEFT JOIN profesores p ON p.usuario_id = u.id
      ORDER BY u.rol, u.apellido'
)->fetchAll();

$pageTitle = 'Usuarios';
$activo = url('admin/usuarios.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>👥 Gestion de usuarios</h2>
    <span class="chip chip-verde"><?= count($usuarios) ?> cuentas</span>
</div>

<?php if ($ok): ?><div class="alerta alerta-ok"><?= e($ok) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

<div class="grid grid-2 mb">
    <div class="card">
        <h3>➕ Crear usuario</h3>
        <form method="post" class="mt" autocomplete="off">
            <input type="hidden" name="accion" value="crear">
            <div class="form-row">
                <div class="form-group"><label>Nombre</label><input name="nombre" required></div>
                <div class="form-group"><label>Apellido</label><input name="apellido" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Usuario</label><input name="username" required></div>
                <div class="form-group"><label>Correo</label><input type="email" name="email" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Contrasena</label><input type="password" name="password" required></div>
                <div class="form-group">
                    <label>Rol</label>
                    <select name="rol" onchange="document.getElementById('extraGroup').style.display=(this.value==='administrador')?'none':'block'">
                        <option value="estudiante">Estudiante</option>
                        <option value="profesor">Profesor</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>
            </div>
            <div class="form-group" id="extraGroup">
                <label>Curso (estudiante) o Especialidad (profesor)</label>
                <input name="extra" placeholder="11-A / Matematicas">
            </div>
            <button class="btn btn-verde btn-block">💾 Crear usuario</button>
        </form>
    </div>

    <div class="card">
        <h3>🔎 Resumen por rol</h3>
        <div class="grid grid-3 mt">
            <?php
            $porRol = ['administrador' => 0, 'profesor' => 0, 'estudiante' => 0];
            foreach ($usuarios as $u) { $porRol[$u['rol']] = ($porRol[$u['rol']] ?? 0) + 1; }
            ?>
            <div class="stat"><div class="valor"><?= $porRol['administrador'] ?></div><div class="etiqueta">Admin</div></div>
            <div class="stat"><div class="valor"><?= $porRol['profesor'] ?></div><div class="etiqueta">Profesores</div></div>
            <div class="stat"><div class="valor"><?= $porRol['estudiante'] ?></div><div class="etiqueta">Estudiantes</div></div>
        </div>
    </div>
</div>

<div class="card">
    <h3>📋 Listado completo</h3>
    <table class="tabla mt">
        <thead>
            <tr><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Detalle</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= e($u['nombre'] . ' ' . $u['apellido']) ?></td>
                <td class="text-dim"><?= e($u['username']) ?></td>
                <td class="text-dim"><?= e($u['email']) ?></td>
                <td><span class="chip <?= $u['rol'] === 'administrador' ? 'chip-rojo' : ($u['rol'] === 'profesor' ? 'chip-ambar' : 'chip-verde') ?>"><?= e($u['rol']) ?></span></td>
                <td class="text-dim"><?= e($u['curso'] ?? $u['especialidad'] ?? '—') ?></td>
                <td><span class="chip <?= $u['activo'] ? 'chip-verde' : 'chip-rojo' ?>"><?= $u['activo'] ? 'activo' : 'inactivo' ?></span></td>
                <td>
                    <div class="flex gap">
                        <form method="post">
                            <input type="hidden" name="accion" value="toggle">
                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                            <button class="btn btn-sm"><?= $u['activo'] ? '⏸' : '▶' ?></button>
                        </form>
                        <form method="post" onsubmit="return confirm('¿Eliminar este usuario y todos sus datos?');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                            <button class="btn btn-sm btn-rojo">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
