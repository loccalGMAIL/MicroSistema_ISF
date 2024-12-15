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
$columns = array("a.legajoAlumno", "a.nombreAlumno", "a.dniAlumno", "a.cursoAlumno", "a.nivelAlumno", "ac.estadoAlumno");
$orderColumn = $columns[$orderColumnIndex];

$whereClause = "";
if (!empty($search)) {
    $whereClause = "WHERE a.nombreAlumno LIKE '%$search%' OR a.dniAlumno LIKE '%$search%' OR a.cursoAlumno LIKE '%$search%' OR a.nivelAlumno LIKE '%$search%'";
}

$totalQuery = "SELECT COUNT(*) AS total FROM alumnos a LEFT JOIN academico ac ON a.idAlumno = ac.idAlumno $whereClause";
$totalResult = $mysqli->query($totalQuery);
$totalData = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

$dataQuery = "SELECT a.legajoAlumno, a.nombreAlumno, a.dniAlumno, a.cursoAlumno, a.nivelAlumno, ac.estadoAlumno
FROM alumnos a LEFT JOIN academico ac ON a.idAlumno = ac.idAlumno $whereClause
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
    "draw" => $draw,
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalData,
    "data" => $data
);

header('Content-Type: application/json');
echo json_encode($response);
?>
