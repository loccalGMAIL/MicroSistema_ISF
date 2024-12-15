<?php
// Configuración de la conexión con PDO
$host = 'localhost'; // Cambia esto si tu base de datos está en otro host
$dbname = 'microsistema_isf'; // Nombre de la base de datos
$username = 'root'; // Tu usuario de base de datos
$password = ''; // Tu contraseña de base de datos

try {
    // Crear la conexión PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Establecer el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Manejo de errores
    echo "Conexión fallida: " . $e->getMessage();
    exit;
}
?>

