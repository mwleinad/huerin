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
		case 'openEditModalFolio':
			$data["form"] = "frm-folios";
			$data['title'] =  "Editar folio";
			$rfc->setRfcId($_POST['rfcId']);
			$info = $rfc->InfoRfc();
			$sucursal->setRfcId($info['rfcId']);
			$sucursales = $sucursal->GetSucursalesByRfc();
			$tiposComprobantes= $main->ListTiposDeComprobantes();
			$folios->setIdSerie($_POST['id']);
			$infoFolios = $folios->getInfoFolios();

			$smarty->assign("data",$data);
			$smarty->assign('rfc', $info);
			$smarty->assign('post', $infoFolios);
			$smarty->assign('tiposComprobantes', $tiposComprobantes);
			$smarty->assign('sucursales', $sucursales);
			$smarty->display(DOC_ROOT.'/templates/boxes/general-popup.tpl');

			break;
		case 'openModalAddFolio':
			$data["form"] = "frm-folios";
			$data['title'] =  "Agregar folio";
			$rfc->setRfcId($_POST['rfcId']);
			$info = $rfc->InfoRfc();
			$sucursal->setRfcId($info['rfcId']);
			$sucursales = $sucursal->GetSucursalesByRfc();
			$tiposComprobantes= $main->ListTiposDeComprobantes();

			$smarty->assign("data",$data);
			$smarty->assign('rfc', $info);
			$smarty->assign('tiposComprobantes', $tiposComprobantes);
			$smarty->assign('sucursales', $sucursales);
			$smarty->display(DOC_ROOT.'/templates/boxes/general-popup.tpl');
		break;
		case 'saveFolios':
			$folios->setProcessLogo(false);
            $rfc->setRfcId($_POST['rfcId']);
            $rfcInfo  = $rfc->InfoRfc();
			$folios->setIdRfc($_POST['rfcId']);
			$folios->setIdEmpresa($rfcInfo['empresaId']);
			$folios->setSerie($_POST['serie']);
			$folios->setFolioInicial($_POST['folio_inicial']);
			$folios->setFolioFinal($_POST['folio_final']);
			$folios->setComprobante($_POST['tiposComprobanteId']);
			$folios->setLugarExpedicion($_POST['lugar_expedicion']);
			$folios->setNoCertificado($_POST['no_certificado']);
			$folios->setEmail($_POST['email']);
			$folios->validateFileLogo();
			if(!$folios->AddFolios()){
				echo 'fail[#]';
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}else{
				echo 'ok[#]';
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');				
				echo '[#]';				
				$smarty->assign('folios', $folios->GetFoliosByRfc());
				$smarty->assign('info', $rfcInfo);
				$smarty->assign('DOC_ROOT', DOC_ROOT);
				$smarty->assign('rfcInfo', $rfcInfo);
				$smarty->display(DOC_ROOT.'/templates/lists/folios.tpl');				
			}//else
			break;
			
		case 'updateFolios':
			$folios->setProcessLogo(false);
			$rfc->setRfcId($_POST['rfcId']);
			$rfcInfo  = $rfc->InfoRfc();
			$folios->setIdSerie($_POST['serieId']);
			$folios->setIdRfc($_POST['rfcId']);
			$folios->setIdEmpresa($rfcInfo['empresaId']);
			$folios->setSerie($_POST['serie']);
			$folios->setFolioInicial($_POST['folio_inicial']);
			$folios->setFolioFinal($_POST['folio_final']);
			$folios->setComprobante($_POST['tiposComprobanteId']);
			$folios->setLugarExpedicion($_POST['lugar_expedicion']);
			$folios->setNoCertificado($_POST['no_certificado']);
			$folios->setEmail($_POST['email']);
			$folios->validateFileLogo();
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
				$smarty->assign('rfcInfo', $rfcInfo);
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
