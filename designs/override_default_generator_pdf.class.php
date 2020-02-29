<?php

class OverrideGenerator extends Main
{
	function GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $nodosConceptos, $empresa, $cancelado = 0)
	{
		global $rfc;
		global $comprobante;

		//Instanciation of inherited class
		$pdf=new PDF('P', 'mm', "a4");
		$pdf->SetMargins(0.5,0.1,0.5);
		$pdf->SetAutoPageBreak(1, 5);		
		
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->AddFont('verdana','','verdana.php');
		$pdf->SetFont('verdana','',7);

		$pdf->SetY(2);
		$pdf->SetX(2);
 		$pdf->SetTextColor(200, 0, 0);
  		$pdf->Cell(20,10,$data["comprobante"]["nombre"],0,0,'C');

		$rootQr = DOC_ROOT.'/empresas/'.$empresa['empresaId'].'/qrs/';
		$qrRfc = strtoupper($data["nodoEmisor"]["rfc"]["rfc"]);
		$nufa = $serie["serieId"].".jpg";
		
		if(file_exists($rootQr.$nufa))
		{
			$pdf->Image($rootQr.$nufa,2,10, 25,25);
		}

		$nufa = $empresa["empresaId"]."_".$serie["serie"]."_".$data["folio"];
		
		$rfcActivo =$data["nodoEmisor"]["rfc"]["rfcId"];
		$root = DOC_ROOT."/empresas/".$empresa["empresaId"]."/certificados/".$rfcActivo."/facturas/pdf/";
		$rootFacturas = DOC_ROOT."/empresas/".$empresa["empresaId"]."/certificados/".$rfcActivo."/facturas/";
		$rootQr = DOC_ROOT."/empresas/".$empresa["empresaId"]."/certificados/".$rfcActivo."/facturas/qr/";
		
		if(!file_exists($rootQr.nufa)){
			$comprobante->GenerateQR($data, $totales, $nodoEmisor, $nodoReceptor, $empresa, $serie);
		}
		
		$pdf->Image($rootQr.$nufa.'.png',180,8, 28, 28);

 		$pdf->SetTextColor(255, 255, 255);
		//block emisor
		$pdf->SetFillColor(30,30,30);
		$pdf->SetDrawColor(30,30,30);
		$pdf->Rect(30, 10, 20, 3, 'DF');

		$xstart = 35;
		$pdf->SetY(8);
		$pdf->SetX($xstart);
		$pdf->Cell(70,8,"Emisor",0);
 		$pdf->SetTextColor(0, 0, 0);

		$pdf->SetFont('verdana','',7);
		$xstart = 30;
		$pdf->SetY(14);
		$pdf->SetX($xstart);
		
		if($data["nodoEmisor"]["rfc"]["razonSocial"] == "IL&#039;DECOR S.A. DE C.V.")
		{
			$data["nodoEmisor"]["rfc"]["razonSocial"] = "test";
		}
		$pdf->MultiCell(80,3,utf8_decode(urldecode($data["nodoEmisor"]["rfc"]["razonSocial"])),0);
		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(80,3,utf8_decode("Direccion: ".urldecode($data["nodoEmisor"]["rfc"]["calle"]." ".$data["nodoEmisor"]["rfc"]["noExt"]." ".$data["nodoEmisor"]["rfc"]["noInt"]." ".$data["nodoEmisor"]["rfc"]["colonia"])),0);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(80,3,utf8_decode(urldecode($data["nodoEmisor"]["rfc"]["municipio"]." ".$data["nodoEmisor"]["rfc"]["estado"]." ".$data["nodoEmisor"]["rfc"]["pais"]." CP: ".$data["nodoEmisor"]["rfc"]["cp"])),0);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(80,3,"RFC: ".$data["nodoEmisor"]["rfc"]["rfc"],0);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(80,3,"Regimen Fiscal: ".utf8_decode(urldecode($data["nodoEmisor"]["rfc"]["regimenFiscal"])),0);

		//block serie
		$pdf->SetFillColor(30,30,30);
 		$pdf->SetTextColor(255, 255, 255);
		$pdf->Rect(112, 10, 25, 22, 'DF');
		$pdf->SetFillColor(255);

		$xstart = 113;
		$pdf->SetY(11);
		$pdf->SetX($xstart);
		$pdf->MultiCell(70,3,"Serie",0);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(70,3,"Folio",0);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(70,3,"Certificado",0);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(70,3,"Fecha",0);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(90,3,utf8_decode("UUID"),0);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(70,3,"Fecha Timbrado",0);

		$xstart = 138;
		$pdf->SetY(10);
		$pdf->SetX($xstart);
		$pdf->SetTextColor(200, 0, 0);
		$pdf->MultiCell(43,3,$serie['serie'],0,0,'R');

		$pdf->SetTextColor(0, 0, 0);
		$xstart = 138;
		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(43,3,$data["folio"],0,0,'R');

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(43,3,$serie['noCertificado'],0,0,'R');

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(43,4,$data['fecha'],0,0,'R');

		$pdf->SetFont('verdana','',5);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(43,3,$data['UUID'],0,0,'R');

		$pdf->SetFont('verdana','',7);

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->MultiCell(43,3,$data['FechaTimbrado'],0,0,'R');

		$pdf->SetY(35);

		$pdf->Line(10,$pdf->GetY()+1,200,$pdf->GetY()+1);
		$pdf->Line(10,$pdf->GetY()+2,200,$pdf->GetY()+2);
		
		//block receptor
		$pdf->SetFillColor(30,30,30);
		$pdf->Rect(2, 40, 25, 3, 'DF');
 		$pdf->SetTextColor(255, 255, 255);
		
		$pdf->SetFont('verdana','',7);
		
		$xstart = 2;
		$pdf->SetY(38);
		$pdf->SetX(6);
		$pdf->MultiCell(70,8,"Receptor",0);
 		$pdf->SetTextColor(0,0,0);
		
		$data["nodoReceptor"]["nombre"] = urlencode($data["nodoReceptor"]["nombre"]);
				
		$infoReceptor = utf8_decode(urldecode($data["nodoReceptor"]["nombre"]));
		$infoReceptor .= "\nDIRECCION: ".utf8_decode(urldecode($data["nodoReceptor"]["calle"]." ".$data["nodoReceptor"]["noExt"]." ".$data["nodoReceptor"]["noInt"]));
		$infoReceptor .= "\n".utf8_decode(urldecode($data["nodoReceptor"]["colonia"]));
		$infoReceptor .= "\n".utf8_decode(urldecode($data["nodoReceptor"]["municipio"]." ".$data["nodoReceptor"]["estado"]." ".$data["nodoReceptor"]["pais"]." CP: ".$data["nodoReceptor"]["cp"]));
				
		$infoReceptor .= "\n".urldecode("RFC: ".$this->Util()->CadenaOriginalVariableFormat($data["nodoReceptor"]["rfc"],false,false));
				
		$xstart = 2;
		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->SetFont('verdana','',7);
		$pdf->SetWidths(array(100));
		$nb = $pdf->WordWrap($infoReceptor, 100);
	    $pdf->Row(
				array($infoReceptor), 3
		);
			
		$pdf->SetFont('verdana','',7);
				
		//block sucursal
//		if($data["nodoEmisor"]["sucursal"]["sucursalActiva"] == 'no'){
/*			$infoSucursal = utf8_decode(urldecode($data["nodoEmisor"]["sucursal"]["identificador"]));
			$infoSucursal .= "\nDIRECCION: ".utf8_decode(urldecode($data["nodoEmisor"]["sucursal"]["calle"]." ".$data["nodoEmisor"]["sucursal"]["noExt"]." ".$data["nodoEmisor"]["sucursal"]["noInt"]));
			$infoSucursal .= "\n".utf8_decode(urldecode($data["nodoEmisor"]["sucursal"]["colonia"]));
			$infoSucursal .= "\n".utf8_decode(urldecode($data["nodoEmisor"]["sucursal"]["municipio"]." ".$data["nodoEmisor"]["sucursal"]["estado"]." ".$data["nodoEmisor"]["sucursal"]["pais"]." \nCP: ".$data["nodoEmisor"]["sucursal"]["cp"]));
*/

		if($empresa['empresaId'] == 21)
		{
			$infoSucursal = utf8_decode(urldecode("NAVARRA 210 PB. COL. ALAMOS"));
			$infoSucursal .= "\n".utf8_decode(urldecode("DEL. BENITO JUAREZ"." DF"."\n"."MEXICO CP: 05300"));
		}
		else
		{
			$infoSucursal = utf8_decode(urldecode($data["nodoEmisor"]["rfc"]["calle"]." ".$data["nodoEmisor"]["rfc"]["noExt"]." ".$data["nodoEmisor"]["rfc"]["noInt"]." \n".$data["nodoEmisor"]["rfc"]["colonia"]));
			$infoSucursal .= "\n".utf8_decode(urldecode($data["nodoEmisor"]["rfc"]["municipio"]." ".$data["nodoEmisor"]["rfc"]["estado"]."\n".$data["nodoEmisor"]["rfc"]["pais"]." CP: ".$data["nodoEmisor"]["rfc"]["cp"]));

		}

			//$infoSucursal = 'Matriz';
			$pdf->SetFillColor(30,30,30);
			$pdf->Rect(105, 40, 35, 3, 'DF');
	 		$pdf->SetTextColor(255, 255, 255);
	
			$xstart = 110;
			$pdf->SetY(38);
			$pdf->SetX($xstart);
			$pdf->MultiCell(70,8,"Lugar de Expedicion:",0);
	 		$pdf->SetTextColor(0,0,0);
			
			$xstart = 105;
			$pdf->SetY($pdf->GetY());
			$pdf->SetX($xstart);

			$pdf->SetFont('verdana','',7);
			$pdf->SetWidths(array(100));
			//$data["observaciones"] = substr(str_replace(array("\r", "\r\n", "\n"), "", $data["observaciones"]), 0, 173);
			$nb = $pdf->WordWrap($infoSucursal, 100);
				$pdf->Row(
					array($infoSucursal), 3
				);
			$pdf->SetFont('verdana','',8);

		
//		}//if
//		else
/*		{
			$infoSucursal = urldecode($data["nodoEmisor"]["sucursal"]["identificador"]."\n . \n . \n .  \n");
			
			$pdf->SetFillColor(30,30,30);
			$pdf->Rect(105, 40, 35, 3, 'DF');
	 		$pdf->SetTextColor(255, 255, 255);
	
			$xstart = 110;
			$pdf->SetY(38);
			$pdf->SetX($xstart);
			$pdf->MultiCell(70,8,"Lugar de Expedicion:",0);
	 		$pdf->SetTextColor(0,0,0);
			
			$xstart = 105;
			$pdf->SetY($pdf->GetY());
			$pdf->SetX($xstart);

			$pdf->SetFont('verdana','',7);
			$pdf->SetWidths(array(100));

			$nb = $pdf->WordWrap($infoSucursal, 100);
				$pdf->Row(
					array( utf8_decode(urldecode($infoSucursal))
					), 3
				);
			$pdf->SetFont('verdana','',8);

		
		}//if */
		
		$setY = $pdf->GetY() + 5;
		$pdf->SetY($setY);
		//$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
		//$pdf->Line(10,$pdf->GetY() + 1,200,$pdf->GetY() + 1);

		//Observaciones
		$pdf->SetY($pdf->GetY() + 7);
		$pdf->SetFont('courier','',7);
		$pdf->SetWidths(array(210));
	    $pdf->Row(
				array( utf8_decode(urldecode($data["observaciones"]))), 3
			);
		$pdf->SetFont('verdana','',7);
		
		//block conceptos
		$pdf->SetFillColor(30,30,30);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->Rect(2, $pdf->GetY()+2, 205, 6, 'DF');

		$xstart = 2;
		$y = $pdf->GetY();
		$pdf->SetY($pdf->GetY()+2);
		$pdf->SetX($xstart);
		$pdf->Cell(15,8,"Cnt.",0,0,"C");
		$pdf->Cell(15,8,"Unid.",0,0,"C");
		$pdf->Cell(20,8,"No. Id",0,0,"C");
		$pdf->Cell(113,8,"Descripcion",0,0,"C");
		$pdf->Cell(17,8,"P. Unit.",0,0,"C");
		$pdf->Cell(22,8,"Importe",0,0,"C");

		$setY = $pdf->GetY()+10;
		$pdf->SetY($setY);
 		$pdf->SetTextColor(0,0,0);
		
		//Table with 20 rows and 4 columns
		$pdf->SetWidths(array(5,15, 17, 25, 100, 22, 22));
		$pdf->SetAligns(array('L','L', 'L', 'L', 'L', 'R', 'R'));
		$pdf->SetFont('courier','',7);
		$xstart = 15;
		$count = 1;
		foreach($nodosConceptos as $concepto)
		{
			if($count % 2 == 0)
			{
				$pdf->SetTextColor(100, 100, 100);
			}
			else
			{
				$pdf->SetTextColor(0, 0, 0);
			}
			$count++;
			
			$xstart = 15;
	   		 $pdf->Row(
				array(
					"",
					$this->Util()->CadenaOriginalVariableFormat($concepto["cantidad"],false,false),
					$this->Util()->CadenaOriginalVariableFormat($concepto["unidad"],false,false),
					$this->Util()->CadenaOriginalVariableFormat($concepto["noIdentificacion"],false,false), 
					utf8_decode($concepto["descripcion"]), 
					"$".$this->Util()->CadenaOriginalPDFFormat($concepto["valorUnitario"], true,false), 
					"$".$this->Util()->CadenaOriginalPDFFormat($concepto["importe"], true,false)
				),3
			);

		}
		
		$pdf->SetTextColor(0, 0, 0);
		//check page break

		$count = 1;
		if($_SESSION["impuestos"])
		{
			$setY = $pdf->GetY();
			$pdf->SetX($xstart);
			$pdf->Cell(40,8,"Otros impuestos o retenciones",0,0,"C");
			$pdf->Ln();
			$pdf->SetTextColor(0, 0, 0);
	
			//Table with 20 rows and 4 columns
			$pdf->SetWidths(array(20, 20, 142, 22));
			$pdf->SetAligns(array('L', 'L', 'L', 'R'));
			
			$xstart = 15;

			if($count % 2 == 0)
			{
				$pdf->SetTextColor(100, 100, 100);
			}
			else
			{
				$pdf->SetTextColor(0, 0, 0);
			}
			$count++;
			
			foreach($_SESSION["impuestos"] as $impuesto)
			{
				$nb = $pdf->WordWrap($impuesto["impuesto"], 80);
				$xstart = 15;
				$pdf->Row(
					array(
						$this->Util()->CadenaOriginalVariableFormat(ucfirst($impuesto["tipo"]),false,false),
						$this->Util()->CadenaOriginalVariableFormat($impuesto["tasa"]."%",false,false),
						$this->Util()->CadenaOriginalVariableFormat($impuesto["impuesto"],false,false), 
						"$".$this->Util()->CadenaOriginalPDFFormat($impuesto["importe"] * count($_SESSION["conceptos"]), true, false) 
					),3
				);
	
			//TODO la parte y el complemento.
		}
		//check page break

		}

		$setY = $pdf->GetY() + 3;
		$pdf->Line(10,$setY,200,$setY);
		$pdf->Line(10,$setY+1,200,$setY+1);

		//block con letra
		$pdf->SetFillColor(30,30,30);
		$pdf->Rect(2, $setY+2, 22, 13, 'DF');
		$pdf->SetTextColor(255, 255, 255);

		$xstart = 3;
		$pdf->SetY($setY+3);
		$pdf->SetX($xstart);
		$pdf->SetFont('verdana','',7);
		$pdf->MultiCell(25,3,"Total Con letra",0);
//		print_r($data);
		$cents = ($totales["total"] - floor($totales["total2"]))*100;
		$totales["total2"] = $totales["total"];
		
		$centavosLetra = $this->Util()->GetCents($totales["total2"]);
		if($cents >= 99)
		{
			$totales["total"] = ceil($totales["total"]);
		}
		else
		{
			$totales["total"] = floor($totales["total"]);
		}
		//Tipo de cambio
		switch($totales["moneda"])
		{
			case "peso": $tiposDeCambio = "Pesos"; $tiposDeCambioSufix = "M.N";break;
			case "dolar": $tiposDeCambio = "Dolares"; $tiposDeCambioSufix = "";break;
			case "euro": $tiposDeCambio = "Euros"; $tiposDeCambioSufix = "";break;
			default: $tiposDeCambio = "Pesos"; $tiposDeCambioSufix = "M.N";break;
		}
		
		$temp = new CNumeroaLetra ();
		$temp->setMayusculas(1);
		$temp->setGenero(1);
		$temp->setMoneda($tiposDeCambio);
		$temp->setDinero(1);
		$temp->setPrefijo('(');
		$temp->setSufijo($tiposDeCambioSufix.')');
		$temp->setNumero($totales["total2"]);
		$letra = $temp->letra();
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetY($setY+3);
		$pdf->SetX(25);
		$pdf->MultiCell(120,3,$letra,0);

		$pdf->SetFont('verdana','',7);

		//add cuenta
		if($data["NumCtaPago"])
		{
			$add = "Numero de Cuenta: ".$data["NumCtaPago"];
		}
				
		$y = $pdf->GetY()+3;
		$pdf->SetY($y);
		$pdf->SetX($xstart);

		$pdf->SetTextColor(255, 255, 255);
		$pdf->MultiCell(25,3,"Tipo De Pago",0);
		$pdf->SetY($y);
		$pdf->SetX(25);
		$pdf->SetTextColor(0,0,0);

		switch($data["metodoDePago"])
		{
			case "01": $metodoPago = "Efectivo"; break;
			case "02": $metodoPago = "Cheque"; break;
			case "03": $metodoPago = "Transferencia"; break;
			case "04": $metodoPago = "Tarjetas de Credito"; break;
			case "05": $metodoPago = "Monederos electronicos"; break;
			case "06": $metodoPago = "Dinero electronico"; break;
			case "08": $metodoPago = "Vales de despensa"; break;
			case "28": $metodoPago = "Tarjeta de Debito"; break;
			case "29": $metodoPago = "Tarjeta de Servicio"; break;
			case "99": $metodoPago = "Otros"; break;
			case "NA": $metodoPago = ""; break;
		}
		//$pdf->MultiCell(120,3,$this->Util()->DecodeVal($data["formaDePago"]."\nMetodo de Pago: ".$metodoPago.". ".$add),0);
		if(!$data["metodoDePago"])
		{
			$data["metodoDePago"] = "No Identificado";
		}
		
		$pdf->MultiCell(120,3,$this->Util()->DecodeVal($data["formaDePago"]."\nMetodo de Pago: ".$data["metodoDePago"]." ".$metodoPago." ".$add),0);


		//block totales
		$pdf->SetFillColor(30,30,30);
		$pdf->Rect(155, $setY+2, 20, 24, 'DF');

		//$pdf->SetFillColor(255);
		$pdf->SetTextColor(255, 255, 255);

		$xstart = 155;
		$pdf->SetY($setY+2);
		$pdf->SetX($xstart);
		//$pdf->SetFillColor(192);
		$pdf->MultiCell(20,3,"Subtotal",0,"C",0);
		$pdf->SetY($pdf->GetY()-3);
		$pdf->SetX($xstart+20);
		$pdf->SetTextColor(0,0,0);
		
		$moneda = "$ ";
		if($totales["moneda"] == "euro")
		{
			$moneda = "EUR ";
		}
		if($totales["moneda"] == "dolar")
		{
			$moneda = "USD ";
		}
		
		$pdf->MultiCell(31,3,$moneda.$this->Util()->CadenaOriginalPDFFormat($totales["subtotal"], true,false),0,"R",0);

		if($totales["descuento"] != 0)
		{
			$pdf->SetY($pdf->GetY());
			$pdf->SetX($xstart);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->MultiCell(20,3,"Descuento",0,"C");
			$pdf->SetY($pdf->GetY()-3);
			$pdf->SetX($xstart+20);
			$pdf->SetTextColor(0,0,0);
			$pdf->MultiCell(31,3,$moneda.$this->Util()->CadenaOriginalPDFFormat($totales["descuento"], true,false),0,"R",0);
		}
		
		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->MultiCell(20,3,$totales["tasaIva"]."% IVA",0,"C");
		$pdf->SetY($pdf->GetY()-3);
		$pdf->SetX($xstart+20);
		$pdf->SetTextColor(0,0,0);
		$pdf->MultiCell(31,3,$moneda.$this->Util()->CadenaOriginalPDFFormat($totales["iva"], true,false),0,"R",0);
//print_r($totales);
		if($totales["retIva"] != 0)
		{
			$pdf->SetY($pdf->GetY());
			$pdf->SetX($xstart);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->MultiCell(20,3,"Ret Iva",0,"C");
			$pdf->SetY($pdf->GetY()-3);
			$pdf->SetX($xstart+20);
			$pdf->SetTextColor(0,0,0);
			$pdf->MultiCell(31,3,$moneda.$this->Util()->CadenaOriginalPDFFormat($totales["retIva"], true,false),0,"R",0);
		}
		
		if($totales["retIsr"] != 0)
		{
			$pdf->SetY($pdf->GetY());
			$pdf->SetX($xstart);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->MultiCell(20,3,"Ret Isr",0,"C");
			$pdf->SetY($pdf->GetY()-3);
			$pdf->SetX($xstart+20);
			$pdf->SetTextColor(0,0,0);
			$pdf->MultiCell(31,3,$moneda.$this->Util()->CadenaOriginalPDFFormat($totales["retIsr"], true,false),0,"R",0);
		}
		
		if($totales["porcentajeIEPS"] != 0)
		{
			$pdf->SetY($pdf->GetY());
			$pdf->SetX($xstart);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->MultiCell(20,3,"IEPS",0,"C");
			$pdf->SetY($pdf->GetY()-3);
			$pdf->SetX($xstart+20);
			$pdf->SetTextColor(0,0,0);
			$pdf->MultiCell(31,3,$moneda.$this->Util()->CadenaOriginalPDFFormat($totales["ieps"], true,false),0,"R",0);
		}

		$pdf->SetY($pdf->GetY());
		$pdf->SetX($xstart);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->MultiCell(20,3,"Total",0,"C");
		$pdf->SetY($pdf->GetY()-3);
		$pdf->SetX($xstart+20);
		$pdf->SetTextColor(0,0,0);
		$pdf->MultiCell(31,3,$moneda.$this->Util()->CadenaOriginalPDFFormat($totales["total2"], true,false),0,"R",0);

		$setY = $pdf->GetY() + 15;
		$pdf->Line(10,$setY+1,200,$setY+1);
		$pdf->Line(10,$setY+2,200,$setY+2);

		$pdf->SetY($pdf->GetY()+10);
		$pdf->SetX($xstart);
		$xstart = 2;
		$pdf->SetX($xstart);

		$pdf->SetFillColor(255);

		$y = $pdf->GetY() + 10;
		$pdf->SetY($y);

		if($data["autorizo"])
		{
			$pdf->MultiCell(60,3,utf8_decode("AUTORIZO\n\n\n".urldecode($data["autorizo"])),0,"C",0);
		}
		$pdf->SetY($y);
		$pdf->SetX(70);
		if($data["recibio"])
		{
			$pdf->MultiCell(60,3,utf8_decode(urldecode("RECIBIO\n\n\n".$data["recibio"])),0,"C", 0);
		}
		$pdf->SetY($y);
		$pdf->SetX(140);
		if($data["vobo"])
		{
			$pdf->MultiCell(60,3,utf8_decode(urldecode("Vo. Bo\n\n\n".$data["vobo"])),0,"C", 0);
		}

		if($data["autorizo"] || $data["recibio"] || $data["vobo"])
		{
			$pdf->CheckPageBreak(15);
			$pdf->SetY($pdf->GetY());
			$pdf->SetX($xstart);
		}
		
		//$pdf->SetX(10);
		$y = $pdf->GetY();
		$pdf->SetY($y+5);
		if($data["reviso"])
		{
			$pdf->MultiCell(90,3,utf8_decode(urldecode("REVISO\n\n\n".$data["reviso"])),0,"C",0);
		}
		
		$pdf->SetY($y+5);
		$pdf->SetX(100);
		
		if($data["pago"])
		{
			$pdf->MultiCell(90,3,utf8_decode(urldecode("PAGO\n\n\n".$data["pago"])),0,"C",0);
		}

		$xstart = 2;
		$pdf->SetY($pdf->GetY()+20);
		$pdf->SetX($xstart);
		$pdf->MultiCell(207,3,utf8_decode("ESTE DOCUMENTO ES UNA REPRESENTACION IMPRESA DE UN CFDI"),0,"C");

		$pdf->SetY($pdf->GetY());
		
		$pdf->SetFont('verdana','',5);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->MultiCell(207,3,"Sello Emisor: ".$data["sello"],0,"L",1);
		$pdf->MultiCell(207,3,"Sello SAT: ".$data["selloSAT"],0,"L",1);

		$cadena["cadenaOriginalTimbre"] = utf8_decode(utf8_decode($data["timbreFiscal"]["original"]));
		$pdf->MultiCell(207,3,"Cadena Original Timbre Fiscal: \n".$cadena["cadenaOriginalTimbre"],0,"L",1);

		if($cancelado){
		
			$xstart = 10;
			$pdf->SetFont('verdana','',36);
			$pdf->SetTextColor(255,00,00);
			$pdf->SetY($setY-15);
			$pdf->SetX($xstart);
			$pdf->Cell(180,8,"C A N C E L A D O",0,0,"C");
					
		}//if

		$nufa = $empresa["empresaId"]."_".$serie["serie"]."_".$data["folio"];
		if($data["nodoEmisor"]["rfc"]["rfc"] == "BABJ701019LD7")
		{
			$id = 20;
		}
		elseif($data["nodoEmisor"]["rfc"]["rfc"] == "BCO160224ECA")
		{

			$id = 21;
		}
		else
		{
			$id = 15;
		}
		$pdf->Output(DOC_ROOT.'/temp/factura_'.$empresa["empresaId"].'.pdf', "F");

	}
}


?>