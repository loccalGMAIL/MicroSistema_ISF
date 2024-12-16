<?php
require_once '../includes/header.php';
require_once '../config/database.php';
require_once '../includes/access_control.php';

// Validar acceso solo para administradores
tieneAcceso(['administrador']);

// Inicializar base de datos
$db = new Database();
$mysqli = $db->getConnection();

// Procesar formulario de creación de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    // Verificar si el usuario ya existe
    $stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "El nombre de usuario ya existe.";
    } else {
        // Insertar nuevo usuario
        $stmt = $mysqli->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario, $password, $rol);
        
        if ($stmt->execute()) {
            $mensaje = "Usuario creado exitosamente.";
        } else {
            $error = "Error al crear el usuario: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="container mt-5">
    <h2>Crear Nuevo Usuario</h2>

    <?php if(isset($mensaje)): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="usuario" class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select class="form-control" id="rol" name="rol" required>
                <option value="administrador">Administrador</option>
                <option value="preceptor">Preceptor</option>
                <option value="cuotas">Cuotas</option>
                <option value="secretaria">Secretaria</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
