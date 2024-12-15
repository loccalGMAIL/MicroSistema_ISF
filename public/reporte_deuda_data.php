<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'conex.php';

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) && $_POST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';
$columns = array("nombreAlumno", "dniAlumno", "cursoAlumno", "nivelAlumno", "deuda");
$orderColumn = $columns[$orderColumnIndex];

// Construir la consulta SQL con el parámetro de búsqueda
$whereClause = "";
if (!empty($search)) {
    $whereClause = "WHERE nombreAlumno LIKE '%$search%' OR dniAlumno LIKE '%$search%' OR cursoAlumno LIKE '%$search%' OR nivelAlumno LIKE '%$search%'";
}

// Obtener el total de registros
$totalQuery = "SELECT COUNT(*) AS total FROM alumnos $whereClause";
$totalResult = $mysqli->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];

// Obtener los datos paginados
$dataQuery = "SELECT nombreAlumno, dniAlumno, cursoAlumno, nivelAlumno, deuda FROM alumnos $whereClause 
ORDER BY $orderColumn $orderDir
LIMIT $start, $length";
$dataResult = $mysqli->query($dataQuery);

$data = array();
if ($dataResult) {
    while ($row = $dataResult->fetch_assoc()) {
        $data[] = $row;
    }
}

$response = array(
    "draw" => intval($draw),
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalData,
    "data" => $data
);

header('Content-Type: application/json');
echo json_encode($response);
?>