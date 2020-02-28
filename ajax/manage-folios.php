<?php

	include_once('../init.php');
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');

	switch($_POST['type']){

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
			/*no esta activa, se tiene que pasa rfcId para btener getrfcActive*/
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