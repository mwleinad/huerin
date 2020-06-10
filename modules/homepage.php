<?php
	
	/* For Session Control - Don't remove this */
	$user->allowAccess();	
	/* End Session Control */
	if($_POST["type"]=="saveAddPendiente")
	{
	  $pendiente->setDescription($_POST["descripcion"]);
	  $pendiente->setPrioridad($_POST["prioridad"]);
		$pendiente->setUsuario($_SESSION["User"]["username"]);
		$pendiente->setFecha(date("Y-m-d"));

	  $id = $pendiente->Save();
		
	}

	 if($_SESSION["avisoadd"])
	  {
		  $msg = "El aviso fue agregado correctamente";
		  $smarty->assign("msg", $msg);
	      unset($_SESSION["avisoadd"]);
	  }

	$notice->SetPage($_GET["p"]);
	$notices = $notice->Enumerate();
	foreach($notices["items"] as $key =>$value)
	{
	  $card = $value;
	  $card["fecha"] = $util->ChangeDateFormat($value["fecha"]);
	  $card["description"] = nl2br($value["description"]);
	  $notices["items"][$key] = $card;
	}

	$smarty->assign("notices", $notices);
	$pendiente->SetPage($_GET["p"]);
	$resArchivo = $pendiente->Enumerate();
	foreach($resArchivo["items"] as $key =>$value)
	{
	  $card = $value;
	  $card["fecha"] = $util->ChangeDateFormat($value["fecha"]);
	  $card["description"] = nl2br($value["description"]);
	  $resArchivo["items"][$key] = $card;
	}
	/*echo "<pre>";
	print_r($resArchivo);
	exit;*/
	$smarty->assign("pendientes", $resArchivo);

?>