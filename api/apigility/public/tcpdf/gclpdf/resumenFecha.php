<?php
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Dario');
$pdf->SetTitle('RESUMEN DE LA FECHA');
$pdf->SetSubject('Grupo Binario');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'RESUMEN DE LA FECHA', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 12, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));


$pdf->setFillcolor(230, 230, 230);

// Set some content to print
$param = $_GET["param"];

//$j = base64_decode($param);

$con = mysql_connect('localhost', 'root', 'frutill4s');
mysql_select_db('torneo');
$sql = 'SELECT reporte_json FROM reporte WHERE reporte_id = '.$param;
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
mysql_free_result($res);
mysql_close($con);

//echo $j; die;
$partidos = json_decode($row[0], true);


$pdf->SetFont('dejavusans', '', 10, '', true);

$borde = 'TB';
$borde = '1';

$ban = '';
foreach ($partidos as $key => $row) {
	$catzona = $row['categoria']." - ".$row['zona'];
	if($catzona != $ban){
		$pdf->Cell(0, 10, $catzona, $borde, 1, 'L', 0);
		$ban = $catzona;
	}

	$pdf->setFillcolor(230, 120, 10);
	$pdf->Cell(85, 10, $row['equipo1'] .'('.$row['goles_eq1'].')', $borde, 0, 'L', 1);
	$pdf->Cell(0, 10, $row['equipo2'] .'('.$row['goles_eq2'].')', $borde, 1, 'L', 1);
	$pdf->setFillcolor(230, 230, 230);

	$pdf->SetFont('dejavusans', 'B', 12, '', true);
	$pdf->Cell(0, 10, 'GOLEADORES', '', 1, 'L', 0);
	$pdf->SetFont('dejavusans', '', 12, '', true);


	foreach ($row['goleadores_eq1'] as $key => $g1) {
		//$pdf->Cell(85, 10, $g1['jugador_nombre'] .' '.$g1['jugador_apellido'] .'('.$g1['goles_cantidad'].')', $borde, 1, 'L', 0);
		$left_column .= $g1['jugador_nombre'] .' '.$g1['jugador_apellido'] .'('.$g1['goles_cantidad'].')' .'<br>';
	}

	foreach ($row['goleadores_eq2'] as $key => $g2) {
		//$pdf->Cell(85, 10, $g2['jugador_nombre'] .' '.$g2['jugador_apellido'] .'('.$g2['goles_cantidad'].')', $borde, 1, 'L', 0);
		$right_column .= $g2['jugador_nombre'] .' '.$g2['jugador_apellido'] .'('.$g2['goles_cantidad'].')'.'<br>';
	}

	// get current vertical position
	$y = $pdf->getY();
	// // set color for background
	$pdf->SetFillColor(255, 255, 255);
  $pdf->writeHTMLCell(85, '', '', $y, $left_column, 0, 0, 1, true, 'J', true);

	$pdf->writeHTMLCell(88, '', '', '', $right_column, 0, 1, 1, true, 'J', true);
	$pdf->Cell(0, 15, '', '', 1, 'L', 0);
//	$pdf->lastPage();

	$right_column = '';
	$left_column = '';
// print_r($partidos); die;
$pdf->SetFont('dejavusans', 'B', 12, '', true);

$pdf->Cell(0, 10, 'AMONESTADOS', '', 1, 'L', 0);
$pdf->SetFont('dejavusans', '', 12, '', true);


foreach ($row['amonestados_eq1'] as $key => $a1) {
	//$pdf->Cell(0, 10, $g1['jugador_nombre'] .' '.$g1['jugador_apellido'] .'('.$g1['goles_cantidad'].')', $borde, 1, 'L', 0);
	$left_column .= $a1['jugador_nombre'] .' '.$a1['jugador_apellido'].'<br>';
}

foreach ($row['amonestados_eq2'] as $key => $a2) {
	//$pdf->Cell(0, 10, $g2['jugador_nombre'] .' '.$g2['jugador_apellido'] .'('.$g2['goles_cantidad'].')', $borde, 1, 'L', 0);
	$right_column .= $a2['jugador_nombre'] .' '.$a2['jugador_apellido'].'<br>';
}


// get current vertical position
$y = $pdf->getY();
$pdf->writeHTMLCell(85, '', '', $y, $left_column, 0, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(88, '', '', '', $right_column, 0, 1, 1, true, 'J', true);
$pdf->Cell(0, 15, '', '', 1, 'L', 0);

$right_column = '';
$left_column = '';

//print_r($partidos); die;
$pdf->SetFont('dejavusans', 'B', 12, '', true);

$pdf->Cell(0, 10, 'EXPULSADOS', '', 1, 'L', 0);
$pdf->SetFont('dejavusans', '', 12, '', true);

foreach ($row['expulsados_eq1'] as $key => $e1) {
	//$pdf->Cell(0, 10, $g1['jugador_nombre'] .' '.$g1['jugador_apellido'] .'('.$g1['goles_cantidad'].')', $borde, 1, 'L', 0);
	$left_column .= $e1['jugador_nombre'] .' '.$e1['jugador_apellido'].'<br>';
}

foreach ($row['expulsados_eq2'] as $key => $e2) {
	//$pdf->Cell(0, 10, $g2['jugador_nombre'] .' '.$g2['jugador_apellido'] .'('.$g2['goles_cantidad'].')', $borde, 1, 'L', 0);
	$right_column .= $e2['jugador_nombre'] .' '.$e2['jugador_apellido'].'<br>';
}


// get current vertical position
$y = $pdf->getY();
$pdf->writeHTMLCell(85, '', '', $y, $left_column, 0, 0, 1, true, 'J', true);
$pdf->writeHTMLCell(88, '', '', '', $right_column, 0, 1, 1, true, 'J', true);
$pdf->Cell(0, 15, '', '', 1, 'L', 0);

$right_column = '';
$left_column = '';


}



// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('fixture.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
