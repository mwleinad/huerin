<?php 

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

$data = $empresa->GetReportePeriodo($_GET["id"]);
//print_r($data);
//exit();
//$util->DP($reporte);

require(DOC_ROOT.'/pdf/fpdf.php');

class PDF extends FPDF
{
	//Page header
	function Header()
	{
		global $data;
		//$contract = new Contract;
		//Logo
		//$this->Image('trazzos-logo.png',10,8,33);		

		$this->AddFont('verdana','','verdana.php');
		$this->SetFont('verdana','',9);
		//Move to the right
		$this->Cell(60);
		//Title
    $this->SetY(5);
 		$this->Cell(0,5,'Reporte Periodo '.$data["periodoId"],0,0,'C');
		$this->Ln();
    $this->SetX(45);
	}

	//Page footer
	function Footer()
	{
			//Position at 1.5 cm from bottom
			$this->SetY(-15);
			//Arial italic 8
			$this->SetFont('Arial','I',8);
			//Page number
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}

	//MultiCell with bullet
	function MultiCellBlt($w, $h, $blt, $txt, $border=0, $align='J', $fill=0)
	{
			//Get bullet width including margins
			$blt_width = $this->GetStringWidth($blt)+$this->cMargin*2;

			//Save x
			$bak_x = $this->x;

			//Output bullet
			$this->Cell($blt_width, $h, $blt, 0, '', $fill);
			//Output text
			$this->MultiCell($w-$blt_width, $h, $txt, $border, $align, $fill);

			//Restore x
			$this->x = $bak_x;
	}
	
//Colored table
	function FancyTable($header,$data)
	{
		//Colors, line width and bold font
		$this->SetFillColor(255,0,0);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B');
		//Header
		$w=array(80,30,30,30,20);
		for($i=0;$i<count($header);$i++)
				$this->Cell($w[$i],6,$header[$i],1,0,'C',true);
		$this->Ln();
		//Color and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		//Data
		$fill=false;
		foreach($data as $row)
		{
				$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
				$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
				$this->Cell($w[2],6,$row[2],'LR',0,'L',$fill);
				$this->Cell($w[3],6,$row[3],'LR',0,'L',$fill);
				$this->Cell($w[4],6,$row[4],'LR',0,'L',$fill);
				$this->Ln();
				$fill=!$fill;
		}
		$this->Cell(array_sum($w),0,'','T');
	}
}

//Instanciation of inherited class
$pdf=new PDF('P', 'mm', 'a4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AddFont('verdana','','verdana.php');
$pdf->AddFont('verdana','B','verdana.php');
$pdf->AddFont('verdana','U','verdana.php');

$pdf->SetY(20);
$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Ganacias Periodo: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(30,5,$data["gananciasTotales"],0);
$pdf->SetFont('verdana','',9);
$pdf->Cell(70,5,"Productos Comprados: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(40,5,$data["productosComprados"],0);
$pdf->Ln();

$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Gastos de Producto: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(30,5,$data["totalPagadoEnProductos"],0);
$pdf->SetFont('verdana','',9);
$pdf->Cell(70,5,"Total Despues de pagar Producto: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(40,5,$data["totalDespuesDePagarProductos"],0);
$pdf->Ln();

$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Clientes Registrados: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(30,5,$data["clientesRegistrados"],0);
$pdf->SetFont('verdana','',9);
$pdf->Cell(70,5,"Clientes en Periodo: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(40,5,$data["clientesEnPeriodo"],0);
$pdf->Ln();

$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Clientes en Linea Activa: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(30,5,$data["clientesEnLineaActiva"],0);
$pdf->SetFont('verdana','',9);
$pdf->Cell(70,5,"Comisiones directas Entregadas: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(40,5,$data["comisionesDirectasEntregadas"],0);
$pdf->Ln();

$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Total Despues de pagar CD: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(30,5,$data["totalDespuesDePagarComisionesDirectas"],0);
$pdf->SetFont('verdana','',9);
$pdf->Cell(70,5,"Comisiones A Repartir: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(40,5,$data["aRepartir"],0);
$pdf->Ln();

$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Precio Por Comision: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(30,5,$data["precioPorComision"],0);
$pdf->SetFont('verdana','',9);
$pdf->Cell(70,5,"Total Pagos Comisiones: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(40,5,$data["totalPagosComisiones"],0);
$pdf->Ln();

$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Total Ganancias del Periodo: ",0);
$pdf->SetFont('verdana','U',9);
$pdf->Cell(30,5,$data["total"],0);
$pdf->Ln();
$pdf->Ln();

$pdf->Ln();
$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Clientes a los que se les pagara comisiones (Linea Directa): ",0);
$pdf->Ln();

if($data["usuariosEnLineaActiva"])
{
	$header=array('Usuario','Activacion de', 'Comision Linear', 'Comision Directa', 'Total');

	foreach($data["usuariosEnLineaActiva"] as $key => $user)
	{
		$row[$key][0]= $key." Id: ".$user["userId"]." Nombre: ".$user["username"];
		$row[$key][1]= $user["pago"]["cantidad"];
		$row[$key][2]= $user["comision"];
		$row[$key][3]= $user["comisionesDirectas"];
		$row[$key][4]= $user["totalComision"];
	}

	$pdf->FancyTable($header,$row);
}
unset($row);

$pdf->Ln();
$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Clientes a los que se les pagara comisiones directas (Linea Inactiva): ",0);
$pdf->Ln();

if($data["usuariosEnLineaInactiva"])
{
	$header=array('Usuario','Activacion de', 'Comision Linear', 'Comision Directa', 'Total');

	foreach($data["usuariosEnLineaInactiva"] as $key => $user)
	{
		$row[$key][0]= $key." Id: ".$user["userId"]." Nombre: ".$user["username"];
		$row[$key][1]= $user["pago"]["cantidad"];
		$row[$key][2]= $user["comision"];
		$row[$key][3]= $user["comisionesDirectas"];
		$row[$key][4]= $user["totalComision"];
	}

	$pdf->FancyTable($header,$row);
}
unset($row);


$pdf->Ln();
$pdf->SetFont('verdana','',9);
$pdf->Cell(50,5,"Clientes que han pagado pero no se les pagara nada (Consumidores): ",0);
$pdf->Ln();

if($data["usuariosConsumidores"])
{
	$header=array('Usuario','Activacion de', 'Comision Linear', 'Comision Directa', 'Total');

	foreach($data["usuariosConsumidores"] as $key => $user)
	{
		$row[$key][0]= $key." Id: ".$user["userId"]." Nombre: ".$user["username"];
		$row[$key][1]= $user["pago"]["cantidad"];
		$row[$key][2]= $user["comision"];
		$row[$key][3]= $user["comisionesDirectas"];
		$row[$key][4]= $user["totalComision"];
	}

	$pdf->FancyTable($header,$row);
}

$pdf->Output();
?>
