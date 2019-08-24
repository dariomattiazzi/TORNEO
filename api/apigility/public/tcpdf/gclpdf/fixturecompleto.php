<?php
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Dario');
$pdf->SetTitle('Horarios');
$pdf->SetSubject('Grupo Binario');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'HORARIOS', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
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
//echo $param; die;

$j = base64_decode($param);

$con = mysql_connect('localhost', 'root', 'frutill4s');
mysql_select_db('torneo');
$sql = 'SELECT reporte_json FROM reporte WHERE reporte_id = '.$j;
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
mysql_free_result($res);
mysql_close($con);

// echo $j; die;
$partidos = json_decode($row[0], true);
//print_r($partidos); die;

$pdf->SetFont('dejavusans', '', 10, '', true);

$borde = 'TB';

foreach ($partidos as $key => $row) {
	$fecha = $key;
	foreach ($row as $key2 => $row2) {
		$categoria = $key2;
		foreach ($row2 as $key3 => $row3) {
			$ban = $categoria." - ".$key3;
			if ($ban != $text) {
				$pdf->SetFont('dejavusans', 'B', 12, '', true);
				$pdf->Cell(0, 10, $categoria." - ".$key3, $borde, 1, 'L', 0);
				$pdf->SetFont('dejavusans', '', 10, '', true);
				$pdf->Cell(0, 10, $fecha, $borde, 1, 'L', 1);
				$text = $ban;
			}else {
				$pdf->Cell(0, 10, $fecha, $borde, 1, 'L', 1);
			}
			foreach ($row3 as $key4 => $row4) {
				$pdf->Cell(30, 0, '', $borde, 0, 'L', 0);
				$pdf->Cell(55, 0, substr($row4['equipo1'], 0, 22), $borde, 0, 'L', 0);
				$pdf->Cell(50, 0, " vs ".$row4['equipo2'], $borde, 0, 'L', 0);
				$pdf->Cell(45, 0, " ", $borde, 1, 'L', 0);
			}
			$pdf->ln();
		}
	}
}

// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('fixture.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
