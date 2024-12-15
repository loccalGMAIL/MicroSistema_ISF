<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'conex.php';

// Variables para DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) && $_POST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';

// Columnas de ordenación según la nueva consulta
$columns = array(
    "alumnos.legajoAlumno",
    "alumnos.nombreAlumno",
    "alumnos.dniAlumno",
    "alumnos.cursoAlumno",
    "alumnos.nivelAlumno",
    "padres.nombrePadre",
    "padres.dniPadre",
    "estado"
);
$orderColumn = $columns[$orderColumnIndex];

// Filtro de búsqueda
$whereClause = "";
if (!empty($search)) {
    $whereClause = "WHERE alumnos.nombreAlumno LIKE '%$search%' 
                    OR alumnos.dniAlumno LIKE '%$search%' 
                    OR alumnos.cursoAlumno LIKE '%$search%' 
                    OR alumnos.nivelAlumno LIKE '%$search%' 
                    OR padres.nombrePadre LIKE '%$search%' 
                    OR padres.dniPadre LIKE '%$search%'";
}

// Consulta para obtener el total de registros
$totalQuery = "
    SELECT COUNT(*) AS total 
    FROM alumnos 
    LEFT JOIN padres ON alumnos.idPadre = padres.idPadre
    $whereClause
";
$totalResult = $mysqli->query($totalQuery);
$totalData = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

// Consulta principal con paginación y ordenación
$dataQuery = "
    SELECT 
        alumnos.idAlumno,
        alumnos.nombreAlumno,
        alumnos.dniAlumno,
        alumnos.legajoAlumno,
        alumnos.cursoAlumno,
        alumnos.nivelAlumno,
        padres.nombrePadre,
        padres.dniPadre,
        CASE 
            WHEN EXISTS (
                SELECT 1
                FROM alumnos a
                WHERE a.idPadre = alumnos.idPadre AND a.deuda IS NULL
            ) THEN 'SIN DATOS'
            WHEN EXISTS (
                SELECT 1
                FROM alumnos a
                WHERE a.idPadre = alumnos.idPadre AND a.deuda = 1
            ) THEN 'No Habilitado'
            ELSE 'Habilitado'
        END AS estado
    FROM 
        alumnos
    LEFT JOIN padres ON alumnos.idPadre = padres.idPadre
    $whereClause
    ORDER BY $orderColumn $orderDir
    LIMIT $start, $length
";
$dataResult = $mysqli->query($dataQuery);

// Procesar los resultados
$data = array();
if ($dataResult) {
    while ($row = $dataResult->fetch_assoc()) {
        $data[] = $row;
    }
}

// Respuesta para DataTables
$response = array(
    "draw" => $draw,
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalData,
    "data" => $data
);

header('Content-Type: application/json');
echo json_encode($response);
?>

