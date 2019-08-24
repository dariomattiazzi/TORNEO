<?php
// Test CVS

require_once 'Excel/reader.php';


// ExcelFile($filename, $encoding);
$data = new Spreadsheet_Excel_Reader();


// Set output Encoding.
$data->setOutputEncoding('CP1251');

/***
* if you want you can change 'iconv' to mb_convert_encoding:
* $data->setUTFEncoder('mb');
*
**/

/***
* By default rows & cols indeces start with 1
* For change initial index use:
* $data->setRowColOffset(0);
*
**/



/***
*  Some function for formatting output.
* $data->setDefaultFormat('%.2f');
* setDefaultFormat - set format for columns with unknown formatting
*
* $data->setColumnFormat(4, '%.3f');
* setColumnFormat - set format for column (apply only to number fields)
*
**/

//$data->read('jprueba.xls');
$data->read('j.xls');

/*


$data->sheets[0]['numRows'] - count rows
$data->sheets[0]['numCols'] - count columns
$data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column

$data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell

$data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
if 'type' == "unknown" - use 'raw' value, because  cell contain value with format '0.00';
$data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format
$data->sheets[0]['cellsInfo'][$i][$j]['colspan']
$data->sheets[0]['cellsInfo'][$i][$j]['rowspan']
*/

error_reporting(E_ALL ^ E_NOTICE);

for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
		//echo utf8_encode("\"".$data->sheets[0]['cells'][$i][$j]."\",");
		//echo $i ." - ".$j; die;

		//		$fechaxls = $data->sheets[0]['cells'][$i][3];
		//		echo $fechaxls."\n";


		$jug[$i]["APELLIDO"]   = utf8_encode($data->sheets[0]['cells'][$i][1]);
		$jug[$i]["NOMBRE"]     = utf8_encode($data->sheets[0]['cells'][$i][2]);
		$jug[$i]["NACIMIENTO"] = $data->sheets[0]['cells'][$i][3];
		$jug[$i]["DNI"]        = utf8_encode($data->sheets[0]['cells'][$i][4]);
		$jug[$i]["EQUIPO"]     = utf8_encode($data->sheets[0]['cells'][$i][5]);
	}
	//echo "\n";
}
//print_r($jug); die;

$sql  = "DELETE FROM jugadorTMP where jugador_id <> 0 AND jugador_id <> 9999;"."\n";
$sql .= "INSERT INTO jugadorTMP (jugador_nombre, jugador_apellido, jugador_dni, jugador_fechanac, jugador_equipo_id) VALUES "."\n";

foreach($jug as $j)
{
	$equipo = $j["EQUIPO"];
	$id_equipo = idequipo($equipo);
	//echo $id_equipo; die;

	$a["ape"] = $j["APELLIDO"];
	$a["nom"] = $j["NOMBRE"];
	$a["nac"] = $j["NACIMIENTO"];
	$a["dni"] = $j["DNI"];
	$a["equ"] = $j["EQUIPO"];
	$a["eid"] = $id_equipo;


	//print_r($a); die;

	$sql.= "('".$j["NOMBRE"]  ."', '" .$j["APELLIDO"] ."', '" .$j["NACIMIENTO"] ."'".", '" .$j["DNI"] ."'" .", " .$id_equipo."),"."\n";

}
echo $sql;

die;

function idequipo($equipo)
{
	//	echo $equipo; die;

	$host = "todalagringa.com.ar";
	$user = "root";
	$pwd = "frutill4s";

	$conexion= @mysql_connect($host, $user, $pwd);
	if (!$conexion) {
		die('Error de conexion n: ' . mysql_error());
	}
	//echo 'Connected successfully'."\n";
	mysql_select_db("torneo", $conexion);

	$queEmp = "SELECT * FROM equipo where equipo_nombre = '$equipo'";
	//echo $queEmp; die;

	$datos = mysql_query($queEmp, $conexion) or die(mysql_error());
	//$totEmp = mysql_num_rows($resEmp);
	//print_r($totEmp); die;

	$f = mysql_fetch_array ($datos);

	//print_r($f["equipo_id"]); die;

	if (!empty($f["equipo_id"])){
		return $f["equipo_id"];
	}else{
		return $equipo;
	}
}
//print_r($data);
//print_r($data->formatRecords);
?>
