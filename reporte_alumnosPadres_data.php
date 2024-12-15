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

// Columnas de ordenación
$columns = array(
    "padres.nombrePadre",
    "padres.dniPadre",
    "alumnos"
);
$orderColumn = $columns[$orderColumnIndex];

// Filtro de búsqueda
$whereClause = "";
if (!empty($search)) {
    $whereClause = "WHERE padres.nombrePadre LIKE '%$search%'
                    OR padres.dniPadre LIKE '%$search%'
                    OR alumnos.nombreAlumno LIKE '%$search%'
                    OR alumnos.dniAlumno LIKE '%$search%'
                    OR alumnos.cursoAlumno LIKE '%$search%'
                    OR alumnos.nivelAlumno LIKE '%$search%'";
}

// Consulta para obtener el total de registros
$totalQuery = "
    SELECT COUNT(DISTINCT padres.idPadre) AS total
    FROM padres
    INNER JOIN alumnos ON padres.idPadre = alumnos.idPadre
    $whereClause
";
$totalResult = $mysqli->query($totalQuery);
$totalData = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

// Consulta principal con paginación y ordenación
$dataQuery = "
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
$whereClause
GROUP BY
    padres.idPadre, padres.nombrePadre, padres.dniPadre
ORDER BY $orderColumn $orderDir
LIMIT $start, $length
";

$dataResult = $mysqli->query($dataQuery);

// Procesar los resultados
$data = array();
if ($dataResult) {
    while ($row = $dataResult->fetch_assoc()) {
        $data[] = array(
            "nombrePadre" => $row['nombrePadre'],
            "dniPadre" => $row['dniPadre'],
            "alumnos" => $row['alumnos']
        );
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