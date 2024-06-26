<?php

//	$empresa->AuthUser();

	$info = $empresa->Info();
	$smarty->assign("info", $info);

//	$empresa->Util()->DB()->setQuery("SELECT * FROM usuario");
//	$result = $empresa->Util()->DB->GetResult();

	$rfc->setEmpresaId($_SESSION["empresaId"], 1);
	$smarty->assign("empresaRfcs", $rfc->GetRfcsByEmpresa());
	switch($_GET["section"]){

		case "nueva-factura":
		     header('location:'.WEB_ROOT);
		    //verificar permisos
            $user->allowAccess(5);
		    $user->allowAccess(131);

			$producto->CleanConceptos();
			$producto->CleanImpuestos();
			$ivas = $main->ListIvas();
			$smarty->assign("ivas", $ivas);
			$retIsrs = $main->ListRetIsr();
			$smarty->assign("retIsrs", $retIsrs);
			$retIvas = $main->ListRetIva();
			$smarty->assign("retIvas", $retIvas);
			$tiposDeMoneda = $main->ListTipoDeMoneda();
			$smarty->assign("tiposDeMoneda", $tiposDeMoneda);
			$comprobantes = $main->ListTiposDeComprobantesValidos();
			$smarty->assign("comprobantes", $comprobantes);
			$sucursal->setRfcId($rfc->getRfcActive());
			$sucursal->setEmpresaId($_SESSION["empresaId"], 1);
			$resSucursales = $sucursal->GetSucursalesByRfc();
			$resSuc = $util->DecodeUrlResult($resSucursales);
			$sucursales = $util->DecodeResult($resSuc);
			$smarty->assign("sucursales", $sucursales);
			$excentoIva = $main->ListExcentoIva();
			$smarty->assign("excentoIva", $excentoIva);
		$smarty->assign('mainMnu','admin_folios');
			break;

		case 'consultar-facturas':
            //verificar permisos
            $user->allowAccess(5);
            $user->allowAccess(132);

			$comprobantes = array();

			$filtros = ['addComplemento' => 1];
			if(!isset($_GET["p"])) {

				unset($_SESSION["filtroFactura"]);

			} elseif (isset($_SESSION['filtroFactura'])) {

				foreach ($_SESSION['filtroFactura'] as $key => $val) {
					$filtros[$key] = $val;
				}
			}
			$comprobante->SetPage($_GET["p"]);
			$result = $comprobante->SearchComprobantesByRfc($filtros);
			$totalFacturas = $result["total"];

			if($result)
			{
				$comprobantes["items"] = $util->DecodeResult($result["items"]);
			}
			$comprobantes["pages"] = $result["pages"];
			$total = 0;
			$subtotal = 0;
			$iva = 0;
			$isr = 0;

			if($comprobantes["items"])
			{
				foreach($comprobantes["items"] as $key => $res){

					if(in_array($res['tiposComprobanteId'], [1,2,3,4]))
					{
						$total += $res['total'];
						$subtotal += $res['subTotal'];
						$iva += $res['ivaTotal'];
						$isr += $res['isrRet'];
					}
				}
			}

			$total = number_format($total,2,'.',',');
			$subtotal = number_format($subtotal,2,'.',',');
			$iva = number_format($iva,2,'.',',');
			$isr = number_format($isr,2,'.',',');
			$smarty->assign('comprobantes',$comprobantes);
			$smarty->assign('filtros',$filtros);
			$smarty->assign('totalFacturas',$totalFacturas);
			$smarty->assign('total',$total);
			$smarty->assign('subtotal',$subtotal);
			$smarty->assign('iva',$iva);
			$smarty->assign('isr',$isr);

			for($k=1; $k<=12; $k++){
				$card['id'] = $k;
				$card['nombre'] = ucfirst($util->ConvertirMes($k));

				$meses[$k] = $card;

			}//for
            $personal->isShowAll();
            $personals = $personal->Enumerate();
            $smarty->assign("personals", $personals);

			$smarty->assign('meses',$meses);

			$tipos_comprobantes = $main->ListTiposDeComprobantes();
			$smarty->assign('tipos_comprobantes',$tipos_comprobantes);
            $smarty->assign('emisores',$rfc->listEmisores());
			break;

		case 'ver-pdf':

            $user->allowAccess(5);
            $user->allowAccess(132);
            $user->allowAccess(134);

			$id_comprobante = $_GET['item'];
			$infoComp = $comprobante->GetInfoComprobante($id_comprobante);
			//crear pdf on the fly
			$enlace = $comprobante->GeneratePdfOnTheFly($infoComp["empresaId"], $infoComp["rfcId"], $infoComp['serie'], $infoComp['folio']);
			header('Location: '.$enlace);
			exit;

			break;

		case 'descargar-pdf':
            //verificar permisos
            $user->allowAccess(5);
            $user->allowAccess(132);
            $user->allowAccess(135);
			$id_comprobante = $_GET['item'];
			$infoComp = $comprobante->GetInfoComprobante($id_comprobante);
			//crear pdf on the fly
			//
			$enlace = $comprobante->GeneratePdfOnTheFly($infoComp["empresaId"], $infoComp["rfcId"], $infoComp['serie'], $infoComp['folio']);
			exit;
			header('Location: '.$enlace);
			exit;
			break;

		case 'descargar-xml':
            //verificar permisos
            $user->allowAccess(5);
            $user->allowAccess(132);
            $user->allowAccess(136);
			$id_comprobante = $_GET['item'];

			$infoComp = $comprobante->GetInfoComprobante($id_comprobante);
			$id_rfc = $infoComp["rfcId"];
			$id_empresa = $infoComp["empresaId"];

			$archivo = "SIGN_".$id_empresa.'_'.$infoComp['serie'].'_'.$infoComp['folio'].'.xml';

			$enlace = WEB_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/xml/'.$archivo;
			//header ("Content-Disposition: attachment; filename=".$archivo."\n\n");
			//header ("Content-Type: application/octet-stream");
			//header ("Content-Length: ".filesize($enlace));
			//readfile($enlace);
			header("location:".WEB_ROOT."/util/download.php?file=".$enlace);
			exit;
			break;
		case 'enviar-pdf':
            header('Location: '.WEB_ROOT);
            exit;
            $user->allowAccess(5);
            $user->allowAccess(132);
            $user->allowAccess(137);
			$id_comprobante = $_GET['item'];

			$infoComp = $comprobante->GetInfoComprobante($id_comprobante);
			$id_rfc = $infoComp["rfcId"];
			$id_empresa = $infoComp["empresaId"];
			$archivo = $id_empresa.'_'.$infoComp['serie'].'_'.$infoComp['folio'].'.xml';

			$enlace = WEB_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/xml/'.$archivo;

			$comprobante->Util()->setError(20011, 'complete');
			$comprobante->Util()->PrintErrors();
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');

			break;

		case 'demo-pdf':
            header('Location: '.WEB_ROOT);
            exit;
			$card["nombre"] = "Asociacion Mexicana de Contadores Publicos, Colegio Profesional en el Estado de Chiapas AC";
			$card["calle"] = "Avenida de los Treboles";
			$card["noExt"] = "123";
			$card["noInt"] = "234-A";
			$card["colonia"] = "Jardines de Tuxtla";
			$card["municipio"] = "Tuxtla Gutierrez";
			$card["estado"] = "Quintana Roo";
			$card["pais"] = "Mexico";
			$card["cp"] = "29060";
			$card["rfc"] = "RFC123456789";

			$data["nodoReceptor"] = $card;

			$comprobante->GenerateDemoPDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $nodosConceptos,$empresa, 0);

			$archivo = 'DemoPDF.pdf';

			$enlace = WEB_ROOT.'/empresas/'.$archivo;

			header ("Content-Disposition: attachment; filename=".$archivo."\n\n");
			header ("Content-Type: application/octet-stream");
			header ("Content-Length: ".filesize($enlace));
			readfile($enlace);


			exit;

			break;

	}//switch


	$rfc->setRfcId(RFC_DEFAULT);
	$certNuevo = $rfc->GetCertificadoByRfc();
	$smarty->assign("certNuevo", $certNuevo);


	$folios->setIdRfc(RFC_DEFAULT);
	$noFolios  = count($listFolios = $folios->GetFoliosByRfc());
	$smarty->assign('noFolios', $noFolios);

	$qrs = 0;
	foreach($listFolios as $key => $value)
	{
		if($value["qr"] != "")
		{
			$qrs++;
		}
	}
	$smarty->assign('qrs', $qrs);

//	$cliente->GetCountClientesByActiveRfc();
	$smarty->assign('countClientes', 1);
	$smarty->assign('mainMnu','invoices');
?>
