<?php

    /* Star Session Control Modules*/
    $user->allowAccess(4);  //level 1
    $user->allowAccess(120);//level 2
    /* end Session Control Modules*/

	$info = $empresa->Info();
	$smarty->assign("info", $info);

	$rfc->setEmpresaId($_SESSION["empresaId"], 1);
	$smarty->assign("empresaRfcs", $rfc->GetRfcsByEmpresa());

	$comprobantes = array();
	$comprobante->SetPage($_GET["p"]);

  if ($_SESSION['fromPayments']) {
    $smarty->assign('sesion',$_SESSION);
  }

  if (!$_SESSION['fromPayments']) {
    $_SESSION['cxc']="";
  } else {
    $_SESSION['fromPayments']=false;
  }


  	if ($_SESSION['cxc'] != "") {
    	$values=$_SESSION['cxc'];
  	} else {
    	$values["mes"] = date("m");
  	}

	// $result = $cxc->SearchCuentasPorCobrar($values);

	$totalFacturas = $result["total"];

	if($result)
	{
		//$comprobantes["items"] = $util->DecodeResult($result["items"]);
		$comprobantes["items"] = $result["items"];
	}
	$comprobantes["pages"] = $result["pages"];

	$total = 0;
	$subtotal = 0;
	$iva = 0;
	$isr = 0;

	if($comprobantes["items"])
	{
		foreach($comprobantes["items"] as $res){
			if($res["tipoDeComprobante"] == "ingreso" && $res["status"] == 1)
			{
				$total += $res['total'];
				$payments += $res['payment'];
				$saldo += $res['saldo'];
			}
		}
	}


	$smarty->assign('comprobantes',$comprobantes);
	$smarty->assign('total',$total);
	$smarty->assign('payments',$payments);
	$smarty->assign('saldo',$saldo);

	$smarty->assign('totalFacturas',$totalFacturas);

	for($k=1; $k<=12; $k++){
		$card['id'] = $k;
		$card['nombre'] = ucfirst($util->ConvertirMes($k));

		$meses[$k] = $card;

	}//for

	$smarty->assign('meses',$meses);

	$departamentos = $departamentos->Enumerate();
	$smarty->assign("departamentos", $departamentos);


  //	$cliente->GetCountClientesByActiveRfc();
	$smarty->assign('countClientes', 1);

	$smarty->assign('mainMnu','cxc');

	if($_SESSION["search"]["month"])
	{
		$month = $_SESSION["search"]["month"];
	}
	else
	{
		$month = date("m");
	}

	if($_SESSION["search"]["year"])
	{
		$year = $_SESSION["search"]["year"];
	}
	else
	{
		$year = date("Y");
	}
	
	$smarty->assign("month", $month);
	$smarty->assign("year", $year);
	
	$personals = $personal->Enumerate();
	$smarty->assign("personals", $personals);

?>