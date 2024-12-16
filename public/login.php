<?php
require_once '../config/database.php';
require_once '../config/session_config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $mysqli = $db->getConnection();

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
//-----
$stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verificar contraseña con password_verify
    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['LAST_ACTIVITY'] = time();
        
        header('Location: index.php');
        exit();
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
} else {
    $error = 'Usuario o contraseña incorrectos.';
}

    //-------
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MicroSistema ISF - Matricula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(to bottom, #1e90ff, #f8f9fa);
        }
        .login-container {
            max-width: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }
        .school-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: -60px auto 10px auto;
            background: #fff;
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
        <!-- Escudo del colegio -->
        <img src="https://isf.edu.py/wp-content/uploads/2020/08/ESCUDO-SIN-FONDO-removebg-preview-218x300.png" 
             alt="Escudo Colegio Sagrada Familia" class="school-logo">
        <h2>Iniciar sesión</h2>
        <!-- Mensaje de error -->
        <?php if (!empty($error)) : ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
