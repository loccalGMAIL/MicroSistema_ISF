<?php
require 'conexion.php'; // Archivo para la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode($_POST['datos'], true);

    if (is_array($datos)) {
        $actualizados = 0;
        $insertados = 0;

        foreach ($datos as $fila) {
            $curso = $fila['curso'];
            $alumno = $fila['alumno'];
            $saldo = $fila['saldo'];

            // Buscar si el alumno ya existe
            $query = $conn->prepare("SELECT idAlumno FROM alumnos WHERE nombreAlumno = ?");
            $query->execute([$alumno]);
            $idAlumno = $query->fetchColumn();

            if ($idAlumno) {
                // Actualizar deuda si el alumno ya existe
                $updateQuery = $conn->prepare("UPDATE deudas SET estadoDeuda = ?, update_at = NOW() WHERE idAlumno = ?");
                $updateQuery->execute([$saldo, $idAlumno]);
                $actualizados++;
            } else {
                // Insertar nuevo alumno y deuda
                $insertAlumnoQuery = $conn->prepare("INSERT INTO alumnos (nombreAlumno, cursoAlumno, create_at) VALUES (?, ?, NOW())");
                $insertAlumnoQuery->execute([$alumno, $curso]);

                $idAlumno = $conn->lastInsertId();

                $insertDeudaQuery = $conn->prepare("INSERT INTO deudas (idAlumno, estadoDeuda, create_at) VALUES (?, ?, NOW())");
                $insertDeudaQuery->execute([$idAlumno, $saldo]);

                $insertados++;
            }
        }

        echo "Procesamiento completado. Registros actualizados: $actualizados. Registros insertados: $insertados.";
        exit;
    } else {
        http_response_code(400);
        echo 'Datos inválidos.';
        exit;
    }
}
?>
