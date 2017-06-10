<?php

	include_once('../init.php');
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');

	switch($_POST['type']){
	
		case 'editFolios':
		
			$id_empresa = $_SESSION['empresaId'];
			
			$sucursal->setEmpresaId($id_empresa, 1);			
			$id_rfc = $sucursal->getRfcActive();
			$sucursal->setRfcId($id_rfc);
			$resSucursales = $sucursal->GetSucursalesByRfc();
			$sucursales = $util->DecodeUrlResult($resSucursales);
			$listComp = $main->ListTiposDeComprobantes();
			
			$ruta_dir = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc;
			
			if(is_dir($ruta_dir)){
				if($gd = opendir($ruta_dir)){
					while($archivo = readdir($gd)){
						$info = pathinfo($ruta_dir.'/'.$archivo);
						if($info['extension'] == 'cer'){
							$nom_certificado = $info['filename'];							
							break;
						}//if
					}//while
					closedir($gd);
				}//if
			}//if
			
			$smarty->assign('nom_certificado', $nom_certificado);
			$smarty->assign('comprobantes', $listComp);
			$smarty->assign('sucursales', $sucursales);
			
			$smarty->assign('DOC_ROOT', DOC_ROOT);
			$folios->setFoliosDelete($_POST['id_serie']);
			$infoFolios = $folios->getInfoFolios();
			
			$infoUser = $user->Info();
			if($infoUser["version"] == "auto")
			{
				$fecha = explode(" ", $infoFolios["noCertificado"]);
				$fechaDate = explode("/", $fecha[0]);
				$fechaTime = explode(":", $fecha[1]);
				$fecha = array_merge($fechaDate, $fechaTime);
				$smarty->assign('fecha', $fecha);				
			}
			$smarty->assign('info', $infoFolios);


			//logo
			$ruta_dir = DOC_ROOT.'/empresas/'.$id_empresa.'/qrs';
			$ruta_web_dir = WEB_ROOT.'/empresas/'.$id_empresa.'/qrs';
			if(is_dir($ruta_dir)){
				if($gd = opendir($ruta_dir)){
					while($archivo = readdir($gd)){
						$serie = explode(".", $archivo);
						if($serie[0] == $_POST['id_serie'])
						{
							$qr = $ruta_web_dir.'/'.$archivo;
							break;
						}
					}//while
					closedir($gd);
				}//if
			}//if
			$smarty->assign('qr', $qr);

			$smarty->assign('infoUser', $infoUser);
			$smarty->display(DOC_ROOT.'/templates/boxes/editar-folios-popup.tpl');
			
			break;
			
		case 'addFolios':
			$id_empresa = $_SESSION['empresaId'];
			$sucursal->setEmpresaId($id_empresa, 1);			
			$id_rfc = $sucursal->getRfcActive();
			$sucursal->setRfcId($id_rfc);
			$resSucursales = $sucursal->GetSucursalesByRfc();
			$sucursales = $util->DecodeUrlResult($resSucursales);
			$listComp = $main->ListTiposDeComprobantes();
			$ruta_dir = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc;
			
			if(is_dir($ruta_dir)){
				if($gd = opendir($ruta_dir)){
					while($archivo = readdir($gd)){
						$info = pathinfo($ruta_dir.'/'.$archivo);
						if($info['extension'] == 'cer'){
							$nom_certificado = $info['filename'];							
							break;
						}//if
					}//while
					closedir($gd);
				}//if
			}//if
			
			$smarty->assign('nom_certificado', $nom_certificado);
			$smarty->assign('comprobantes', $listComp);
			$smarty->assign('sucursales', $sucursales);
			$smarty->assign('DOC_ROOT', DOC_ROOT);
			
			$info = $user->Info();
			$smarty->assign('info', $info);
			$smarty->display(DOC_ROOT.'/templates/boxes/agregar-folios-popup.tpl');break;
			
			break;	
			
		case 'saveFolios':
		
			$info = $user->Info();
			$values = explode('&', $_POST['form']);
			foreach($values as $key => $val){
				$values[$key] = explode('=', $values[$key]);
			}
			
			$id_rfc = $rfc->getRfcActive();
			
			$folios->setIdRfc($id_rfc);
			$folios->setIdEmpresa($_SESSION['empresaId']);
			
						
			$folios->setSerie($values[0][1]);
			$folios->setFolioInicial($values[1][1]);
			$folios->setFolioFinal($values[2][1]);
			
			if($info["version"] == "v3" || $info["version"] == "construc")
			{
				$folios->setComprobante($values[3][1]);
				$folios->setLugarExpedicion($values[4][1]);			
				$folios->setNoCertificado($values[5][1]);
				$folios->setEmail($values[6][1]);
			}
			elseif($info["version"] == "auto")
			{
							$fecha = $values[4][1]."/".$values[5][1]."/".$values[6][1]." ".$values[7][1].":".$values[8][1].":".$values[9][1];
							$folios->setNoAprobacion($values[3][1]);
							$folios->setComprobante($values[10][1]);
							$folios->setLugarExpedicion($values[11][1]);			
							$folios->setNoCertificado($fecha);
							$folios->setEmail($values[12][1]);
			}
	
			
			if(!$folios->AddFolios()){
				echo 'fail[#]';
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}else{
				echo 'ok[#]';
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');				
				echo '[#]';				
				$smarty->assign('folios', $folios->GetFoliosByRfc());
				$smarty->assign('info', $info);
				$smarty->assign('DOC_ROOT', DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/folios.tpl');				
			}//else
			break;
			
		case 'saveEditFolios': 
			$info = $user->Info();
			$values = explode('&', $_POST['form']);
			foreach($values as $key => $val){
				$values[$key] = explode('=', $values[$key]);
			}
			
			$id_rfc = $rfc->getRfcActive();
			//print_r($values);
			$folios->setIdRfc($id_rfc);
			$folios->setIdEmpresa($_SESSION['empresaId']);					
			$folios->setSerie($values[0][1]);
			$folios->setFolioInicial($values[1][1]);
			$folios->setFolioFinal($values[2][1]);
echo "jere";
print_r($info);
echo $info["version"];
			if($info["version"] == "v3" || $info["version"] == "construc")
			{
				$folios->setComprobante($values[3][1]);
				$folios->setLugarExpedicion($values[4][1]);			
				$folios->setNoCertificado($values[5][1]);
				$folios->setEmail($values[6][1]);
				$folios->setIdSerie($values[7][1]);
			}
			elseif($info["version"] == "auto")
			{
				$folios->setNoAprobacion($values[3][1]);
				
				//juntar fecha
				$fecha = $values[4][1]."/".$values[5][1]."/".$values[6][1]." ".$values[7][1].":".$values[8][1].":".$values[9][1];
				$folios->setComprobante($values[10][1]);
				$folios->setLugarExpedicion($values[11][1]);			
				$folios->setNoCertificado($fecha);
				$folios->setEmail($values[12][1]);
				$folios->setIdSerie($values[13][1]);			
			}
			
			if(!$folios->EditFolios()){
				echo 'fail[#]';
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}else{
				echo 'ok[#]';
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo '[#]';
				$smarty->assign('folios', $folios->GetFoliosByRfc());
				$smarty->assign('DOC_ROOT', DOC_ROOT);
				$smarty->assign('info', $info);
				$smarty->display(DOC_ROOT.'/templates/lists/folios.tpl');
			}
			break;	
		case 'deleteFolios':
				$info = $user->Info();
				$folios->setFoliosDelete($_POST['id_serie']);
				$folios->setEmpresaId($_SESSION['empresaId'], 1);
				if($folios->DeleteFolios()){
					echo 'Ok[#]';
					$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
					echo '[#]';
					$id_rfc = $rfc->getRfcActive();
					$folios->setIdRfc($id_rfc);
					$folios = $folios->GetFoliosByRfc();
			  		$smarty->assign('folios', $folios);
					$smarty->assign('DOC_ROOT', DOC_ROOT);
					$smarty->assign('info', $info);
					$smarty->display(DOC_ROOT.'/templates/lists/folios.tpl');
				}
				break;
		
	}//switch

?>
