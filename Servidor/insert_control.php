<?php

include 'urls.php';

header("Access-Control-Allow-Origin: ".$ip);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Content-Length");

$db_host = $ip. ":3306";
$db_username = "root";
$db_password = "1234";
$db_name = "laboratorio";
$db_table = "control";

$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($mysqli->connect_error) {
	die("Connection failed: " . $mysqli->connect_error);
}

$datos = file_get_contents('php://input');
$datos_json = json_decode($datos, false);

$cliente = $datos_json->cliente;
$usuario = $datos_json->usuario;
$topic = $datos_json->topic;
$mensaje = $datos_json->mensaje;
$valores = explode('.',$mensaje);


$lab = $valores[0];
if ($valores[1] == "start" || $valores[1] == 'stop') {
	$elemento = 'medidor';
	if ($valores[1] == "start") {
		$estado = 1;
	} else {
		$estado = 0;
	}
} else {
	$elemento = $valores[1];
	if ($valores[2] == "start") {
		$estado = 1;
	} else {
		$estado = 0;
	}
}

$result1 = $mysqli->query("UPDATE control set estado = '$estado' where laboratorio = '$lab' and elemento = '$elemento'");
$result2 = $mysqli->query("insert ignore into control_log (
	laboratorio, 
	elemento,
    estado 
) VALUES (
	'$lab', 
	'$elemento',
    '$estado')
");

$mysqli->close();

