<?php
require_once '../includes/header.php';
require_once '../config/database.php';
require_once '../includes/access_control.php';

// Validar acceso solo para administradores
tieneAcceso(['administrador']);

// Inicializar base de datos
$db = new Database();
$mysqli = $db->getConnection();

// Obtener ID de usuario a editar
$id_usuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del usuario
$stmt = $mysqli->prepare("SELECT id, usuario, rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_nuevo = $_POST['usuario'];
    $rol = $_POST['rol'];
    $password = !empty($_POST['password']) 
        ? password_hash($_POST['password'], PASSWORD_DEFAULT) 
        : null;

    if ($password) {
        // Actualizar usuario con contraseña
        $stmt = $mysqli->prepare("UPDATE usuarios SET usuario = ?, rol = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $usuario_nuevo, $rol, $password, $id_usuario);
    } else {
        // Actualizar usuario sin cambiar contraseña
        $stmt = $mysqli->prepare("UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("ssi", $usuario_nuevo, $rol, $id_usuario);
    }

    if ($stmt->execute()) {
        $mensaje = "Usuario actualizado exitosamente.";
        // Actualizar datos del usuario para mostrar en el formulario
        $usuario['usuario'] = $usuario_nuevo;
        $usuario['rol'] = $rol;
    } else {
        $error = "Error al actualizar el usuario: " . $stmt->error;
    }
    $stmt->close();
}
?>

<div class="container mt-5">
    <h2>Editar Usuario</h2>

    <?php if(isset($mensaje)): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if($usuario): ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="usuario" class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" 
                   value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña (dejar en blanco si no se cambia)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select class="form-control" id="rol" name="rol" required>
                <option value="administrador" <?= $usuario['rol'] == 'administrador' ? 'selected' : '' ?>>Administrador</option>
                <option value="preceptor" <?= $usuario['rol'] == 'preceptor' ? 'selected' : '' ?>>Preceptor</option>
                <option value="cuotas" <?= $usuario['rol'] == 'cuotas' ? 'selected' : '' ?>>Cuotas</option>
                <option value="secretaria" <?= $usuario['rol'] == 'secretaria' ? 'selected' : '' ?>>secretaria</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
    <?php else: ?>
        <div class="alert alert-danger">Usuario no encontrado.</div>
    <?php endif; ?>
</div>