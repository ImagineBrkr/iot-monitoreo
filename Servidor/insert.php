<?php

include 'urls.php';

header("Access-Control-Allow-Origin: 127.0.0.1");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Content-Length");
date_default_timezone_set('America/Lima');

$db_host = $ip. ":3306";
$db_username = "root";
$db_password = "1234";
$db_name = "laboratorio";
$db_table = "mqtt_mensajes";

$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($mysqli->connect_error) {
	die("Connection failed: " . $mysqli->connect_error);
}

$datos = file_get_contents('php://input');
$datos_json = json_decode($datos, false);

$cliente = $datos_json->cliente;
$lab = $datos_json->usuario;
$topic = $datos_json->topic;
$mensaje = $datos_json->mensaje;

$result = $mysqli->query("select id, fecha from medicion_wh where laboratorio = '$lab' order by fecha desc limit 1");

if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		$id_wh = $row["id"];
	  $fecha = $row["fecha"];
	}
  } else {
	$fecha = '2000/01/01 01:01:01';
}

$result = $mysqli->query("select id, fecha, medicion from medicion where laboratorio = '$lab' order by fecha desc limit 1");

if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		$id_w = $row["id"];
	  $fechaw = $row["fecha"];
      $ultimaMed = $row["medicion"];
	}
  } else {
	$fechaw = '2000/01/01 01:01:01';
}

$fecha = strtotime($fecha);

$fechaw = strtotime($fechaw);

$actual = date('Y-m-d-H-i-s');
$valoresActual = explode('-',$actual);

$seg = 60 - $valoresActual[5];
$min = 59 - $valoresActual[4];

$fechaIngreso = DateTime::createFromFormat('Y-m-d-H-i-s', $actual);
$actual = $fechaIngreso->format('Y-m-d-H-i-s');

$fechaIngresoMedicionWh = new Datetime($fechaIngreso->format('Y-m-d H:i:s'));
$feMedicion = new Datetime($fechaIngreso->format('Y-m-d H:i:s'));

$fechaIngresoMedicionWh->add(new DateInterval('PT'.$min.'M'.$seg.'S'));
$feMedicion = date_add($feMedicion, DateInterval::createFromDateString($seg.' seconds'));

if (date('Y-m-d-H-i-s',$fecha) == $fechaIngresoMedicionWh->format('Y-m-d-H-i-s')) {
    $result = $mysqli->query("update medicion_wh set medicion = '$mensaje->energy' where id = '$id_wh'");
} else {
	$valores = $fechaIngresoMedicionWh->format('Y-m-d-H-i-s');
	$valores = explode('-',$valores);
    $result = $mysqli->query("insert into medicion_wh (laboratorio,medicion,fecha) values ('$lab', '$mensaje->energy', '".$fechaIngresoMedicionWh->format('Y-m-d')."')");
	$result = $mysqli->query("update medicion_wh set fecha = DATE_ADD(DATE_ADD(fecha, INTERVAL ".$valores[3]." HOUR), INTERVAL ".$valores[4]." MINUTE) where laboratorio = '$lab' and fecha = '".$fechaIngresoMedicionWh->format('Y-m-d')."'");

}

if (date('Y-m-d-H-i-s',$fechaw) == $feMedicion->format('Y-m-d-H-i-s')) {
    $ultimaMed = $ultimaMed + $mensaje->power;
    $result = $mysqli->query("update medicion set medicion = '$ultimaMed' where id = '$id_w'");
} else {
	$valores = $feMedicion->format('Y-m-d-H-i-s');
	$valores = explode('-',$valores);
    $result = $mysqli->query("insert into medicion (laboratorio,medicion,fecha) values ('$lab', '$mensaje->power', '".$feMedicion->format('Y-m-d')."')");
	$result = $mysqli->query("update medicion set fecha = DATE_ADD(DATE_ADD(fecha, INTERVAL ".$valores[3]." HOUR), INTERVAL ".$valores[4]." MINUTE) where laboratorio = '$lab' and fecha = '".$feMedicion->format('Y-m-d')."'");
}

$mysqli->close();
?>
