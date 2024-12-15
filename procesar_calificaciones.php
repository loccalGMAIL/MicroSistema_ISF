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

        $legajo = $fila[0];
        $previas = $fila[3];
        $recuperatorio = $fila[4];

        // Verificar si el alumno existe en la tabla de alumnos
        $query = "SELECT idAlumno FROM alumnos WHERE legajoAlumno = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $legajo);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            // Alumno existe, obtener idAlumno
            $row = $result->fetch_assoc();
            $idAlumno = $row['idAlumno'];
            $estadoAlumno = ($previas + $recuperatorio) > 3 ? 1 : 0;

            // Verificar si el registro ya existe en la tabla academico
            $query = "SELECT * FROM academico WHERE idAlumno = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("i", $idAlumno);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows > 0) {
                // Actualizar el estado del alumno en la tabla academico
                $query = "UPDATE academico SET estadoAlumno = ? WHERE idAlumno = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ii", $estadoAlumno, $idAlumno);
                $stmt->execute();
                $registrosActualizados++;
            } else {
                // Insertar un nuevo registro en la tabla academico
                $query = "INSERT INTO academico (idAlumno, estadoAlumno) VALUES (?, ?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ii", $idAlumno, $estadoAlumno);
                $stmt->execute();
                $registrosInsertados++;
            }
        }
    }

    echo "Procesamiento completado. Registros actualizados: $registrosActualizados. Registros insertados: $registrosInsertados.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
