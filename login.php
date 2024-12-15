<?php
session_start();
require_once 'conex.php'; // Cambié el nombre del archivo de conexion.php a conex.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Usando la conexión mysqli
    // $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($mysqli->connect_error) {
        die("Conexión fallida: " . $mysqli->connect_error);
    }

    // Preparar la consulta
    $stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = ?");
    $stmt->bind_param("ss", $usuario, $password); // 'ss' significa que ambos parámetros son cadenas
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Usuario encontrado, iniciamos la sesión
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $user['usuario'];
        header('Location: index.php');
        exit();
    } else {
        echo 'Usuario o contraseña incorrectos.';
    }

    $stmt->close();
    // $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <!-- Enlace a Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
            margin: 0;
        }
        .login-container {
            max-width: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2 class="text-center">Iniciar sesión</h2>
        <!-- Mensaje de error -->
        <?php if (!empty($error)) : ?>
            <p class="error-message text-center"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- Formulario -->
        <form method="post" action="login.php">
            <div class="mb-3">
                <label for="nombre_usuario" class="form-label">Usuario</label>
                <input type="text" id="nombre_usuario" name="usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <input type="password" id="contrasena" name="password" class="form-control" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </div>
        </form>
    </div>

    <!-- Enlace a los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

