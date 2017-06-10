<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('document-basic');	
	/* End Session Control */
	
	session_start();
	
	if($_POST['action'] == 'save'){
		
		$docSelladoId = $_POST['docSelladoId'];
				
		$type = $_FILES['archivo']['type'];
		$archivo = $_FILES["archivo"]['name'];
		$prefijo = substr(md5(uniqid(rand())),0,6);
		
		if($archivo != ''){		
			if($type == 'application/pdf' || $type == 'image/pjpeg' || $type == 'image/jpeg'){
				
				$ext = ($type == 'application/pdf') ? 'pdf':'jpg';
							
				//Guardamos el archivo a la carpeta pdfs
				$fileName = date('ymd').'_s'.$prefijo.'.'.$ext;
				
				$destino =  DOC_ROOT.'/temp/'.$fileName;
				if (@copy($_FILES['archivo']['tmp_name'],$destino)){
					
					$card['archivo'] = $fileName;
											
					$_SESSION['sellado'][$docSelladoId] = $card;
					
					$_SESSION['msgCmp'] = 'El archivo fue guardado correctamente.';
					header('Location: '.WEB_ROOT.'/sellado-files/docSelladoId/'.$docSelladoId);
					exit;
					
				}else{
					$msgError = "Ocurri&oacute; un error al subir archivo. <br> Por favor, intentelo de nuevo.";
				}
			}else{
				$msgError = 'El tipo de archivo es incorrecto debe ser .pdf o .jpg';
			}
		}else{
			$msgError = 'Debe elegir el archivo';	
		}
		
	}
	
	if($_GET['docSelladoId']){
		
		$docSelladoId = $_GET['docSelladoId'];
						
	}elseif($_GET['delKey']){
		
		$docSelladoId = $_GET['delKey'];
				
		$card = $_SESSION['sellado'][$docSelladoId];
			
		$fileName = $card['archivo'];
		$card['archivo'] = '';
		$card['edit'] = 0;
					
		$_SESSION['sellado'][$docSelladoId] = $card;
		
		@unlink(DOC_ROOT.'/temp/'.$fileName);
		@unlink(DOC_ROOT.'/archivos/'.$fileName);
		
		$sql = 'UPDATE contract_docsellado SET archivo = "" WHERE archivo LIKE "'.$fileName.'"';
		$util->DB()->setQuery($sql);
		$util->DB()->UpdateData();
		
		$_SESSION['msgCmp'] = 'El archivo fue eliminado correctamente.';
		
	}else		
		$docSelladoId = $_GET['docSelladoId'];
		
	$inFile = $_SESSION['sellado'][$docSelladoId];

	$docSellado->setDocBasicId($docSelladoId);
	$infD = $docSellado->Info();
	
	$cmpMsg = $_SESSION['msgCmp'];
	$_SESSION['msgCmp'] = '';
	
	$infD['name'] = strtoupper(utf8_decode($infD['name']));
		
	$smarty->assign('showForm', $showForm);
	$smarty->assign('cmpMsg', $cmpMsg);
	$smarty->assign('msgError', $msgError);
	$smarty->assign('idKey', $idKey);
	$smarty->assign('k', $k);
	$smarty->assign('docSelladoId', $docSelladoId);
	$smarty->assign('infD', $infD);
	$smarty->assign('inFile', $inFile);
	$smarty->assign('mainMnu','catalogos');

?>