<?php
require 'vendor/autoload.php';
require 'conex.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    $ruta_temporal = $archivo['tmp_name'];

    try {
        // Cargamos el archivo desde la ruta temporal
        $documento = IOFactory::load($ruta_temporal);
        $hojaActual = $documento->getSheet(0);
        $numeroFilas = $hojaActual->getHighestDataRow();

        for ($indiceFila = 1; $indiceFila <= $numeroFilas; $indiceFila++) {
            $dniAlumno = $hojaActual->getCell('A' . $indiceFila)->getValue();
            $dniPadre = $hojaActual->getCell('B' . $indiceFila)->getValue();

            if (!empty($dniAlumno) && !empty($dniPadre)) {
                // Verificar si el padre ya existe en la tabla padres
                $consultaPadre = $mysqli->prepare("SELECT idPadre FROM padres WHERE dniPadre = ?");
                $consultaPadre->bind_param("s", $dniPadre);
                $consultaPadre->execute();
                $resultadoPadre = $consultaPadre->get_result();
                $idPadre = null;

                if ($resultadoPadre->num_rows > 0) {
                    // Si existe, obtener el idPadre
                    $fila = $resultadoPadre->fetch_assoc();
                    $idPadre = $fila['idPadre'];
                } 

                // Actualizar la tabla alumnos con el idPadre obtenido
                $actualizarAlumno = $mysqli->prepare("UPDATE alumnos SET idPadre = ? WHERE dniAlumno = ?");
                $actualizarAlumno->bind_param("is", $idPadre, $dniAlumno);
                $actualizarAlumno->execute();
            }
        }
        $resultado = "Archivo procesado correctamente";
    } catch (Exception $e) {
        $resultado = "Error al procesar el archivo: " . $e->getMessage();
    }
    echo $resultado;
} else {
    echo "No se recibió ningún archivo";
}



require 'header.php';
?>

<!-- Contenido Principal -->
<div class="container mt-5">
    <h1>Resultado de la importacion:</h1>
    <br>
    <br>
    <h2 style="color:green; font-weight:bold"><?php echo $resultado ?></h2>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


