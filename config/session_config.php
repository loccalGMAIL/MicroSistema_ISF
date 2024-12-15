<?php
// Configurar tiempo de vida de la sesión
ini_set('session.gc_maxlifetime', 6000); // 10 minutos
ini_set('session.cookie_lifetime', 6000); // 10 minutos

function checkSessionExpiration() {
    // Si no hay sesión iniciada, no hacer nada
    if (session_status() == PHP_SESSION_NONE) {
        return;
    }

    // Si han pasado más de 30 minutos, destruir la sesión
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        // Destruir la sesión
        session_unset();     // Eliminar todas las variables de sesión
        session_destroy();   // Destruir la sesión
        
        // Redirigir al login
        header("Location: login.php?message=session_expired");
        exit();
    }

    // Actualizar el último momento de actividad
    $_SESSION['LAST_ACTIVITY'] = time();
}