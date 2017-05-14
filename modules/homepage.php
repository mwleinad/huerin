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

	if($_POST["type"]=="saveAddNotice")
	{
	   $ruta = DOC_ROOT.'/archivos';
	   $tamano = $_FILES["path"]['size'];
	   $tipo = $_FILES["path"]['type'];
	   $archivo = $_FILES["path"]['name'];
	   $extension = explode(".",$archivo);
	  
	  $notice->setDescription($_POST["descripcion"]);
	  $notice->setPrioridad($_POST["prioridad"]);
		$notice->setUsuario($_SESSION["User"]["username"]);
		$notice->setFecha(date("Y-m-d"));

		if($_FILES["path"]['name'])
	  {
	     $prefijo = "notice_".$_POST["usuario"].$id;
			 $fileName = $prefijo.".".end($extension);
			 $destino =  $ruta."/".$fileName;
	  }

	  $notice->setPath($fileName);
		echo "here";
	  $id = $notice->Save();

	  $notice->setPath($fileName);

	  $notice->setNoticeId($id);

		if($_FILES["path"]['name'])
	  {
	     $prefijo = "notice_".$_POST["usuario"].$id;
			 $fileName = $prefijo.".".end($extension);
			 $destino =  $ruta."/".$fileName;
	  }
	  $notice->setPath($fileName);
	  $notice->Update();

	  header('Location: '.WEB_ROOT.'/homepage');
	}
	if($_SESSION["avisoadd"])
	  {
		  $msg = "El aviso fue agregado correctamente";
		  $smarty->assign("msg", $msg);
	      unset($_SESSION["avisoadd"]);
	  }

	$notice->SetPage($_GET["p"]);
	$resArchivo = $notice->Enumerate();
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
	$smarty->assign("notices", $resArchivo);

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