<?php
require_once '../includes/header.php';
require_once '../config/database.php';
require_once '../includes/access_control.php';

// Validar acceso solo para administradores
tieneAcceso(['administrador']);

// Inicializar base de datos
$db = new Database();
$mysqli = $db->getConnection();

// Procesar eliminación de usuario
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id_usuario = $_GET['eliminar'];
    $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $mensaje = "Usuario eliminado exitosamente.";
    } else {
        $error = "No se pudo eliminar el usuario.";
    }
    $stmt->close();
}

// Obtener lista de usuarios
$result = $mysqli->query("SELECT id, usuario, rol FROM usuarios");
?>

<div class="container mt-5">
    <h2>Administración de Usuarios</h2>

    <?php if(isset($mensaje)): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <a href="crear_usuario.php" class="btn btn-primary mb-3">Crear Nuevo Usuario</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($usuario = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                    <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="usuarios.php?eliminar=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro que desea eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
