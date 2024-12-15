<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'conex.php';

// Consulta SQL para obtener los datos
$query = "
SELECT 
    padres.idPadre,
    padres.nombrePadre,
    padres.dniPadre,
    GROUP_CONCAT(
        CONCAT(alumnos.nombreAlumno, ' (', alumnos.dniAlumno, ') - ', alumnos.cursoAlumno, ' - ', alumnos.nivelAlumno)
        SEPARATOR '\n'
    ) AS alumnos
FROM 
    padres
INNER JOIN 
    alumnos ON padres.idPadre = alumnos.idPadre
GROUP BY 
    padres.idPadre, padres.nombrePadre, padres.dniPadre
";

$result = $mysqli->query($query);

if ($result) {
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    die("Error en la consulta: " . $mysqli->error);
}

// Mostrar los resultados en una tabla HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Agrupada por Padres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Listado Agrupado por Padres</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre del Padre</th>
                    <th>DNI del Padre</th>
                    <th>Alumnos Asociados</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nombrePadre'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['dniPadre'] ?? '') ?></td>
                            <td><?= nl2br(htmlspecialchars($row['alumnos'] ?? 'No hay alumnos asociados')) ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No se encontraron datos</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

