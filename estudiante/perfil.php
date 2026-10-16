<?php
/**
 * estudiante/perfil.php
 * ---------------------------------------------------------------------
 * Edicion de datos personales + SUBIR FOTO DE PERFIL.
 * La foto se guarda en /uploads/avatares y se muestra en la sesion.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['estudiante'];
require_once __DIR__ . '/../includes/guard.php';

/** @var \App\Models\Estudiante $usuario */
$ok = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'datos';

    // ---------- Accion 1: actualizar datos personales ----------
    if ($accion === 'datos') {
        $nombre   = trim((string) ($_POST['nombre'] ?? ''));
        $apellido = trim((string) ($_POST['apellido'] ?? ''));
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($nombre === '' || $apellido === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Revisa los campos: nombre, apellido y un correo valido.';
        } else {
            $usuario->actualizarDatos([
                'nombre' => $nombre, 'apellido' => $apellido,
                'email' => $email, 'password' => $password,
            ]);
            $_SESSION['nombre'] = $usuario->getNombreCompleto();
            $ok = 'Datos actualizados correctamente. 🎸';
        }
    }

    // ---------- Accion 2: subir foto de perfil ----------
    if ($accion === 'foto') {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $error = 'No se recibio ninguna imagen valida.';
        } elseif ($_FILES['foto']['size'] > MAX_UPLOAD_BYTES) {
            $error = 'La imagen supera el limite de 2 MB.';
        } else {
            $info = @getimagesize($_FILES['foto']['tmp_name']);
            $permitidos = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp'];
            if ($info === false || !isset($permitidos[$info[2]])) {
                $error = 'Formato no permitido. Usa JPG, PNG, GIF o WEBP.';
            } else {
                if (!is_dir(UPLOAD_DIR)) {
                    @mkdir(UPLOAD_DIR, 0775, true);
                }
                $ext    = $permitidos[$info[2]];
                $nombre = sprintf('u%d_%s.%s', $usuario->getId(), bin2hex(random_bytes(6)), $ext);
                $destino = UPLOAD_DIR . '/' . $nombre;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                    // Borrar la foto anterior si existia.
                    $vieja = $usuario->getFoto();
                    if ($vieja && is_file(APP_ROOT . '/' . $vieja)) {
                        @unlink(APP_ROOT . '/' . $vieja);
                    }
                    $usuario->guardarFoto('uploads/avatares/' . $nombre);
                    $ok = '¡Tu foto de perfil se actualizo! 📸';
                } else {
                    $error = 'No se pudo guardar la imagen en el servidor.';
                }
            }
        }
    }
}

$pageTitle = 'Mi perfil';
$activo = url('estudiante/perfil.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>📸 Mi perfil</h2></div>

<?php if ($ok): ?><div class="alerta alerta-ok"><?= e($ok) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

<div class="grid grid-2">
    <!-- ============ FOTO DE PERFIL ============ -->
    <div class="card text-center">
        <img src="<?= e($usuario->getFotoUrl()) ?>" alt="Foto actual"
             style="width:180px;height:180px;border-radius:50%;object-fit:cover;border:5px solid var(--green-500);box-shadow:0 10px 30px rgba(0,0,0,.6)">
        <h3 class="mt"><?= e($usuario->getNombreCompleto()) ?></h3>
        <p class="text-dim"><?= e($usuario->descripcionRol()) ?></p>

        <form method="post" enctype="multipart/form-data" class="mt">
            <input type="hidden" name="accion" value="foto">
            <div class="form-group">
                <label for="foto">Subir nueva foto (JPG/PNG/GIF/WEBP, max 2 MB)</label>
                <input type="file" id="foto" name="foto" accept="image/*" required>
            </div>
            <button class="btn btn-ambar btn-block">⬆ Subir foto</button>
        </form>
    </div>

    <!-- ============ DATOS PERSONALES ============ -->
    <div class="card">
        <h3>Datos personales</h3>
        <form method="post" class="mt">
            <input type="hidden" name="accion" value="datos">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= e($usuario->getNombre()) ?>" required>
                </div>
                <div class="form-group">
                    <label>Apellido</label>
                    <input type="text" name="apellido" value="<?= e($usuario->getApellido()) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Correo electronico</label>
                <input type="email" name="email" value="<?= e($usuario->getEmail()) ?>" required>
            </div>
            <div class="form-group">
                <label>Nueva contrasena (dejar vacio para conservar)</label>
                <input type="password" name="password" placeholder="••••••••">
            </div>
            <button class="btn btn-verde btn-block">💾 Guardar cambios</button>
        </form>

        <hr style="border-color:rgba(255,255,255,.1);margin:1.2rem 0">
        <table class="tabla tabla-mini">
            <tr><td>Usuario</td><td><strong><?= e($usuario->getUsername()) ?></strong></td></tr>
            <tr><td>Codigo</td><td><?= e($usuario->getCodigo()) ?></td></tr>
            <tr><td>Curso</td><td><?= e($usuario->getCurso()) ?></td></tr>
            <tr><td>Acudiente</td><td><?= e($usuario->getAcudiente()) ?></td></tr>
            <tr><td>Rol</td><td><span class="chip chip-verde"><?= e($usuario->getRol()) ?></span></td></tr>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
