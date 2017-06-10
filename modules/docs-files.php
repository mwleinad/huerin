<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('document-basic');	
	/* End Session Control */
	
	session_start();
	
	if($_POST['action'] == 'save'){
		
		$k = $_POST['k'];
		$docBasicId = $_POST['docBasicId'];
				
		$type = $_FILES['archivo']['type'];
		$archivo = $_FILES["archivo"]['name'];
		$prefijo = substr(md5(uniqid(rand())),0,6);
		
		if($archivo != ''){		
			if($type == 'application/pdf' || $type == 'image/pjpeg' || $type == 'image/jpeg'){
				
				$ext = ($type == 'application/pdf') ? 'pdf':'jpg';
							
				//Guardamos el archivo a la carpeta pdfs
				$fileName = date('ymd').'_'.$prefijo.'.'.$ext;
				
				$destino =  DOC_ROOT.'/temp/'.$fileName;
				if (@copy($_FILES['archivo']['tmp_name'],$destino)){
					
					$resDocs = $_SESSION['docs'][$docBasicId];
			
					$card = $resDocs[$k];
					$card['archivo'] = $fileName;
					$resDocs[$k] = $card;
						
					$_SESSION['docs'][$docBasicId] = $resDocs;
					
					$_SESSION['msgCmp'] = 'El archivo fue guardado correctamente.';
					header('Location: '.WEB_ROOT.'/docs-files/docBasicId/'.$docBasicId);
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
	
	if($_GET['idKey']){
		
		$idKey = $_GET['idKey'];
		
		$val = explode('-',$idKey);
		$docBasicId = $val[0];
		$k = $val[1];
		$showForm = 1;
		
	}elseif($_GET['delKey']){
		
		$delKey = $_GET['delKey'];
		
		$val = explode('-',$delKey);
		$docBasicId = $val[0];
		$k = $val[1];
		
		$resDocs = $_SESSION['docs'][$docBasicId];
			
		$card = $resDocs[$k];
		$fileName = $card['archivo'];
		$card['archivo'] = '';
		$card['edit'] = 0;
		$resDocs[$k] = $card;
			
		$_SESSION['docs'][$docBasicId] = $resDocs;
		
		@unlink(DOC_ROOT.'/temp/'.$fileName);
		@unlink(DOC_ROOT.'/archivos/'.$fileName);
		
		$sql = 'UPDATE docbasic_docs SET archivo = "" WHERE archivo LIKE "'.$fileName.'"';
		$util->DB()->setQuery($sql);
		$util->DB()->UpdateData();
		
		$_SESSION['msgCmp'] = 'El archivo fue eliminado correctamente.';
		
	}else		
		$docBasicId = $_GET['docBasicId'];
	
	$_SESSION['docId'] = $docBasicId;
	
	$resDocs = $_SESSION['docs'][$docBasicId];
	
	$docBasic->setDocBasicId($docBasicId);
	$infD = $docBasic->Info();
	
	$cmpMsg = $_SESSION['msgCmp'];
	$_SESSION['msgCmp'] = '';
	
	$infD['name'] = strtoupper(utf8_decode($infD['name']));
	$infD['info'] = strtoupper(utf8_decode($infD['info']));
	
	$smarty->assign('showForm', $showForm);
	$smarty->assign('cmpMsg', $cmpMsg);
	$smarty->assign('msgError', $msgError);
	$smarty->assign('idKey', $idKey);
	$smarty->assign('k', $k);
	$smarty->assign('docBasicId', $docBasicId);
	$smarty->assign('infD', $infD);
	$smarty->assign('resDocs', $resDocs);
	$smarty->assign('mainMnu','catalogos');

?>