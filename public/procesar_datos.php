<?php
// procesar_datos.php
require 'conex.php';

try {
    // Recibir los datos
    $datos = json_decode($_POST['datos'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar JSON");
    }

    $registrosActualizados = 0;
    $registrosInsertados = 0;

    foreach($datos as $fila) {
        $curso = $fila[0];
        $alumno = $fila[1];
        $saldo = $fila[2];

        // Verificar si el alumno existe en la tabla de alumnos
        $query = "SELECT nombreAlumno, deuda FROM alumnos WHERE nombreAlumno = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $alumno);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            // Alumno existe, actualizar deuda
            $row = $result->fetch_assoc();
            $deuda = $saldo != 0 ? 1 : 0;

            $query = "UPDATE alumnos SET deuda = ? WHERE nombreAlumno = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("is", $deuda, $alumno);
            $stmt->execute();
            $registrosActualizados++;
        }
    }

    echo "Procesamiento completado. Registros actualizados: $registrosActualizados. Registros insertados: $registrosInsertados.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>