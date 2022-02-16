<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

//include_once(DOC_ROOT.'/classes/generate_xml_default.class.php');

switch($_POST["type"])
{
	case "agregarImpuesto":
		$values = explode("&", $_POST["form"]);
		foreach($values as $key => $val)
		{
			$values[$key] = explode("=", $values[$key]);
		}
		$producto->setTasa($values[0][1]);
		$producto->setImpuesto($values[1][1]);
		$producto->setTipo($values[4][1]);

		$producto->setTasaIva($values[2][1]);
		$producto->setImporteImpuesto($values[3][1]);

		if(!$producto->AgregarImpuesto())
		{
			echo "fail|";
			$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
		}
		else
		{
			echo "ok|ok";
		}
		echo "|";
		$smarty->assign("impuestos", $_SESSION["impuestos"]);
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->display(DOC_ROOT.'/templates/lists/impuestos.tpl');
		break;

	case "agregarConcepto":
		$values = explode("&", $_POST["form"]);
		foreach($values as $key => $val)
		{
			$values[$key] = explode("=", $values[$key]);
		}
		$producto->setCantidad($values[0][1]);
		$producto->setNoIdentificacion($values[1][1]);
		$producto->setUnidad($values[2][1]);
		$producto->setDescripcion($values[5][1]);
		$producto->setValorUnitario($values[3][1]);
		$producto->setExcentoIva($values[4][1]);
		$producto->setCategoriaConcepto($values[6][1]);
		$producto->setImporte();
		if(!$producto->AgregarConcepto())
		{
			echo "fail|";
			$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
		}
		else
		{
			echo "ok|ok";
		}
		echo "|";
    $conceptos = $util->DecodeUrlResult($_SESSION["conceptos"]);
		$smarty->assign("conceptos", $conceptos);

		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->display(DOC_ROOT.'/templates/lists/conceptos.tpl');

		break;
	case "borrarConcepto":
		$producto->BorrarConcepto($_POST["id"]);
    $conceptos = $util->DecodeUrlResult($_SESSION["conceptos"]);
		$smarty->assign("conceptos", $conceptos);
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->display(DOC_ROOT.'/templates/lists/conceptos.tpl');

		break;
	case "borrarImpuesto":
		$producto->BorrarImpuesto($_POST["id"]);
		$smarty->assign("impuestos", $_SESSION["impuestos"]);
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->display(DOC_ROOT.'/templates/lists/impuestos.tpl');

		break;
	case "updateTotalesDesglosados":
		$totalDesglosado = $producto->GetTotalDesglosado();
		$smarty->assign("impuestos", $totalDesglosado["impuestos"]);
		unset($totalDesglosado["impuestos"]);
		if($totalDesglosado){
			foreach($totalDesglosado as $key => $total)
			{
				$totalDesglosado[$key] = number_format($totalDesglosado[$key], 2);
			}
		}
		$smarty->assign("totalDesglosado", $totalDesglosado);
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->display(DOC_ROOT.'/templates/boxes/total-desglosado.tpl');

		break;
	case "generarComprobante":
		$data["datosFacturacion"] = $_POST["nuevaFactura"];
		$data["observaciones"] = $_POST["observaciones"];

		$data["reviso"] = $_POST["reviso"];
		$data["autorizo"] = $_POST["autorizo"];
		$data["recibio"] = $_POST["recibio"];
		$data["vobo"] = $_POST["vobo"];
		$data["pago"] = $_POST["pago"];

		$data["spf"] = $_POST["spf"];
		$data["isn"] = $_POST["isn"];
		$data["facturador"] = $_POST["facturador"];


		if(!$comprobante->GenerarComprobante($data))
		{
			echo "fail|";
			$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
		}
		else
		{
			echo "ok|";
			$info = $user->Info();
			$smarty->assign("info", $info);
			$comprobante = $comprobante->GetLastComprobante();
			$smarty->assign("comprobante", $comprobante);
			$smarty->display(DOC_ROOT.'/templates/boxes/export-factura.tpl');
		}

		break;
	case "cambiarRfcActivo":
		$rfc->setRfcId($_POST["rfcId"]);
		$rfc->SetAsActive();
		break;
	case "vistaPreviaComprobante":

		$data["datosFacturacion"] = $_POST["nuevaFactura"];
		$data["observaciones"] = $_POST["observaciones"];

		$data["reviso"] = $_POST["reviso"];
		$data["autorizo"] = $_POST["autorizo"];
		$data["recibio"] = $_POST["recibio"];
		$data["vobo"] = $_POST["vobo"];
		$data["pago"] = $_POST["pago"];

		$data["spf"] = $_POST["spf"];
		$data["isn"] = $_POST["isn"];
		$data["facturador"] = $_POST["facturador"];

		if($vistaPrevia->vistaPreviaComprobante($data))
		{
			$com["path"] = urlencode(WEB_ROOT."/");
			$smarty->assign("comprobante", $com);
			echo "";
			$smarty->display(DOC_ROOT.'/templates/boxes/export-vista-previa.tpl');
		}
		else
		{
			echo "fail|";
			$smarty->assign('DOC_ROOT', DOC_ROOT);
      $smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
		}
		break;
}

?>
