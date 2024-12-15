<?php
require 'vendor/autoload.php';
require 'conex.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if(isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    $ruta_temporal = $archivo['tmp_name'];
    
    try {
        // Cargamos el archivo desde la ruta temporal
        $documento = IOFactory::load($ruta_temporal);
        $hojaActual = $documento->getSheet(0);
        $numeroFilas = $hojaActual->getHighestDataRow();
        $letra = $hojaActual->getHighestColumn();

        for($indiceFila = 1; $indiceFila <= $numeroFilas; $indiceFila++) {
            $valor1 = $hojaActual->getCell('A' . $indiceFila)->getValue();
            $valor2 = $hojaActual->getCell('B' . $indiceFila)->getValue();
            $valor3 = $hojaActual->getCell('C' . $indiceFila)->getValue();
            $valor4 = $hojaActual->getCell('D' . $indiceFila)->getValue();
            
            $sql = "INSERT INTO alumnos (nombreAlumno, dniAlumno, cursoAlumno, nivelAlumno) VALUES ('$valor1','$valor2','$valor3','$valor4')";
            // $mysqli->query($sql);
            
            if($valor1 != 0){
                $mysqli->query($sql);
            }
        }
        $resultado = "Archivo procesado correctamente";
    } catch(Exception $e) {
        $resultado = "Error al procesar el archivo: " . $e->getMessage();
    }
} else {
    $resultado = "No se recibió ningún archivo";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Microsistema Escolar - Matricula 2024</title>
    <!-- Enlace a la versión más reciente de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <!-- Enlace a la librería de ExcelJS para cargar archivos XLSX -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.2.1/exceljs.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Enlace a DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">______ Microsistema Escolar - ISF</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.html">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="deuda.html">Deuda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calificaciones.html">Calificaciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reportes.html">Reportes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="configuracion.html">Configuración ______</a>
                </li>
            </ul>
        </div>
    </nav>

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


