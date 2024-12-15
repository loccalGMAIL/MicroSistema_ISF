<?php 

$mysqli = new mysqli('localhost','root','','microsistema_isf');
// $mysqli = new mysqli('srv1432.hstgr.io','u207432813_adm','2=QppVHPc','u207432813_matricula');
if($mysqli->connect_errno){
    echo 'Fallo la conexion '. $mysqli->connect_error;
    die();
};