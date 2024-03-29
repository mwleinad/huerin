<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

switch($_POST["action"])
{
	case "edit":
	case "save":
			if($_POST["type"] == "Persona Moral")
			{
				$_POST['regimenId'] = $_POST['regimenIdMoral'];
			}
			//informacion basica
			$contract->setType($_POST['type']);
			$contract->setFacturador($_POST['facturador']);
			$contract->setName($_POST['name']);
			$contract->setRfc($_POST['rfc']);
			$contract->setSociedadId($_POST['sociedadId']);
			$contract->setRegimenId($_POST['regimenId']);
			if(isset($_POST['actividad_comercial']))
				$contract->setActividadComercialId($_POST['actividad_comercial']);
			//direccion fiscal
			$contract->setAddress($_POST['address']);
			$contract->setNoExtAddress($_POST['noExtAddress']);
			$contract->setNoIntAddress($_POST['noIntAddress']);
			$contract->setColoniaAddress($_POST['coloniaAddress']);
			$contract->setMunicipioAddress($_POST['municipioAddress']);
			$contract->setEstadoAddress($_POST['estadoAddress']);
			$contract->setPaisAddress($_POST['paisAddress']);
			$contract->setCpAddress($_POST['cpAddress']);
			$contract->setMetodoDePago($_POST['metodoDePago']);
			//direccion comercial
			$contract->setDireccionComercial($_POST['direccionComercial']);

			//datos de contacto
    		if(isset($_POST['nameContactoAdministrativo']))
				$contract->setNameContactoAdministrativo($_POST['nameContactoAdministrativo']);
    		if(isset($_POST['emailContactoAdministrativo']))
				$contract->setEmailContactoAdministrativo($_POST['emailContactoAdministrativo']);
    		if(isset($_POST['telefonoContactoAdministrativo']))
    			$contract->setTelefonoContactoAdministrativo($_POST['telefonoContactoAdministrativo']);
			if(isset($_POST['nameContactoContabilidad']))
				$contract->setNameContactoContabilidad($_POST['nameContactoContabilidad']);
    		if(isset($_POST['emailContactoContabilidad']))
				$contract->setEmailContactoContabilidad($_POST['emailContactoContabilidad']);
    		if(isset($_POST['telefonoContactoContabilidad']))
				$contract->setTelefonoContactoContabilidad($_POST['telefonoContactoContabilidad']);
			if(isset($_POST['nameRepresentanteLegal']))
				$contract->setNameRepresentanteLegal($_POST['nameRepresentanteLegal']);
    		if(isset($_POST['nameContactoDirectivo']))
				$contract->setNameContactoDirectivo($_POST['nameContactoDirectivo']);
    		if(isset($_POST['emailContactoDirectivo']))
    			$contract->setEmailContactoDirectivo($_POST['emailContactoDirectivo']);
    		if(isset($_POST['telefonoContactoDirectivo']))
    			$contract->setTelefonoContactoDirectivo($_POST['telefonoContactoDirectivo']);
    		if(isset($_POST['telefonoCelularDirectivo']))
    			$contract->setTelefonoCelularDirectivo($_POST['telefonoCelularDirectivo']);
			//contraseñas
			//contraseñas
			if(isset($_POST['claveFiel']))
				$contract->setClaveFiel($_POST['claveFiel']);
			if(isset($_POST['claveCiec']))
				$contract->setClaveCiec($_POST['claveCiec']);
			if(isset($_POST['claveIdse']))
				$contract->setClaveIdse($_POST['claveIdse']);
			if(isset($_POST['claveIsn']))
				$contract->setClaveIsn($_POST['claveIsn']);
			if(isset($_POST['claveSip']))
				$contract->setClaveSip($_POST['claveSip']);
			if($_POST['use_alternative_rz_for_invoice'] === '1') {
				$contract->setUseAlternativeRzForInvoice(1);
				$contract->setAlterntiveRzId($_POST['alternative_rz_id']);
				$contract->setSeparateInvoice(isset($_POST['createSeparateInvoice']) ? 1 : 0);
				if($_POST['alternative_rz_id'] === '0') {
					$contract->setAlternativeType($_POST['alternativeType']);
					$contract->setAlternativeRz($_POST['alternativeRz']);
					$contract->setAlternativeRfc($_POST['alternativeRfc']);
					$contract->setAlternativeCp($_POST['alternativeCp']);
					$contract->setAlternativeRegimen($_POST['alternativeRegimen']);
					$contract->setAlternativeUsoCfdi($_POST['alternativeUsoCfdi']);
					$contract->setSeparateInvoice(1);
				}
			}
	        $contract->setQualification($_POST['idTipoClasificacion']);
	        $contract->setClaveUsoCfdi($_POST['claveUsoCfdi']);
    		$validation = $contract->Validate();
			if($validation)
			{
				echo "ok[#]";
			}
			else
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
			}
		break;

	case 'loadDocGral':

			$contSubcatId = $_POST['contSubcatId'];

			echo 'ok[#]';

			$smarty->assign("DOC_ROOT", DOC_ROOT);

			if($contSubcatId == 1 || $contSubcatId == 3)
				$smarty->display(DOC_ROOT.'/templates/lists/enumStatusProm.tpl');
			else
				$smarty->display(DOC_ROOT.'/templates/lists/enumStatusDef.tpl');
			echo '[#]';

		break;

	case 'loadCities':

			$stateId = $_POST['stateId'];

			$city->setStateId($stateId);
			$cities = $city->Enumerate();
			$cities = $util->EncodeResult($cities);

			echo 'ok[#]';

			$smarty->assign("cities", $cities);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/enumCity.tpl');

		break;

	case 'loadCitiesC':

			$stateId = $_POST['stateId'];

			$city->setStateId($stateId);
			$cities = $city->Enumerate();
			$cities = $util->EncodeResult($cities);

			echo 'ok[#]';

			$smarty->assign("cities", $cities);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/enumCityC.tpl');

		break;

	case 'addProrrogaDiv':

			$docGralId = $_POST['docGralId'];
			$resProrroga = $_SESSION['prorroga'][$docGralId];

			$f['d'] = date('j');
			$f['m'] = date('n');
			$f['y'] = date('Y');

			$smarty->assign("f", $f);
			$smarty->assign('docGralId', $docGralId);
			$smarty->assign("resProrroga", $resProrroga);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-prorroga-popup.tpl');

		break;

	case 'saveAddProrroga':

			$docGralId = $_POST['docGralId'];
			$contract->setYear($_POST['year']);

			$fecha = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];

			$contract->setDocGralId($docGralId);
			$contract->setFechaProrroga($fecha);

			if(!$contract->SaveProrrogaTemp())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{

				echo "ok[#]";

				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');

				echo "[#]";

				$resProrroga = $_SESSION['prorroga'][$docGralId];

				$smarty->assign('docGralId', $docGralId);
				$smarty->assign("resProrroga", $resProrroga);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/prorroga.tpl');

				echo '[#]';

				if($resProrroga){
					echo '<b>Prorrogas:</b><br>';
					foreach($resProrroga as $res)
						echo $util->setFormatDate($res).'<br>';
				}

			}

		break;

	case 'deleteProrroga':

			$k = $_POST['k'];
			$docGralId = $_POST['docGralId'];

			if(!$contract->DeleteProrrogaTemp($k, $docGralId))
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{

				echo "ok[#]";

				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');

				echo "[#]";

				$resProrroga = $_SESSION['prorroga'][$docGralId];

				$smarty->assign('docGralId', $docGralId);
				$smarty->assign("resProrroga", $resProrroga);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/prorroga.tpl');

				echo '[#]';

				if($resProrroga){
					echo '<b>Prorrogas:</b><br>';
					foreach($resProrroga as $res)
						echo $util->setFormatDate($res).'<br>';
				}

			}

		break;

	case 'addDocsDiv':

			$docBasicId = $_POST['docBasicId'];
			$resDocs = $_SESSION['docs'][$docBasicId];

			$docBasic->setDocBasicId($docBasicId);
			$infD = $docBasic->Info();

			$f['d'] = date('j');
			$f['m'] = date('n');
			$f['y'] = date('Y');

			$smarty->assign("f", $f);
			$smarty->assign('titInfo', $infD['info']);
			$smarty->assign('docBasicId', $docBasicId);
			$smarty->assign("resDocs", $resDocs);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-docs-popup.tpl');

		break;

	case 'saveAddDocs':

			$docBasicId = $_POST['docBasicId'];
			$contract->setYear($_POST['year']);

			$fecha = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];

			$contract->setDocBasicId($docBasicId);
			$contract->setFechaDoc($fecha);
			$contract->setDesc($_POST['des']);

			if(!$contract->SaveDocTemp())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{

				echo "ok[#]";

				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');

				echo "[#]";

				$resDocs = $_SESSION['docs'][$docBasicId];

				$docBasic->setDocBasicId($docBasicId);
				$infD = $docBasic->Info();

				$smarty->assign('titInfo', $infD['info']);
				$smarty->assign('docBasicId', $docBasicId);
				$smarty->assign("resDocs", $resDocs);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/docs.tpl');

				echo '[#]';

				foreach($resDocs as $key => $res){
					if($key != 0)
						echo $util->setFormatDate($res['fecha']).'<br>';
				}

				echo '[#]';

				foreach($resDocs as $key => $res){
					if($key != 0)
						echo $res['desc'].'<br>';
				}

			}

		break;

	case 'deleteDocs':

			$k = $_POST['k'];
			$docBasicId = $_POST['docBasicId'];

			if(!$contract->DeleteDocsTemp($k, $docBasicId))
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{

				echo "ok[#]";

				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');

				echo "[#]";

				$resDocs = $_SESSION['docs'][$docBasicId];

				$docBasic->setDocBasicId($docBasicId);
				$infD = $docBasic->Info();

				$smarty->assign('titInfo', $infD['info']);
				$smarty->assign('docBasicId', $docBasicId);
				$smarty->assign("resDocs", $resDocs);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/docs.tpl');

				echo '[#]';

				foreach($resDocs as $key => $res){
					if($key != 0)
						echo $util->setFormatDate($res['fecha']).'<br>';
				}

				echo '[#]';

				foreach($resDocs as $key => $res){
					if($key != 0)
						echo $res['desc'].'<br>';
				}

			}

		break;

	case 'getDocsList':

			$docBasicId = $_SESSION['docId'];

			echo $docBasicId;
			echo '[#]';

			$resDocs = $_SESSION['docs'][$docBasicId];

			if(!$resDocs)
				$resDocs = array();

			foreach($resDocs as $key => $res){
				if($key != 0)
					echo $util->setFormatDate($res['fecha']).'<br>';
			}

			echo '[#]';

			foreach($resDocs as $key => $res){
				if($key != 0)
					echo $res['desc'].'<br>';
			}

			echo '[#]';

			foreach($resDocs as $key => $res){
				if($res['archivo']){

					$folder = ($res['edit'] == 1) ? 'archivos' : 'temp';

					echo '<a href="'.WEB_ROOT.'/'.$folder.'/'.$res['archivo'].'" target="_blank">
						  <img src="'.WEB_ROOT.'/images/icons/file.png" border="0" />
						  </a>';
				}
				echo '<br>';
			}


		break;
	case 'loadUsoCfdi':
		$regimen = $_POST['regimen'] ?? false;
		$persona = $_POST['persona'] ?? false;
		$contrato = $_POST['contractId'] ?? false;
		$alterno = $_POST['alterno'] ?? false;

		$contratoInfo = [];
       if($contrato) {
		   $contract->setContractId($contrato);
		   $contractInfo = $contract->Info();
	   }

		$ftr = "";
		switch($persona) {
			case 'Persona Moral': $ftr .=" AND tax_purpose IN(1,3) "; break;
			case 'Persona Fisica': $ftr .=" AND tax_purpose IN(2,3) ";break;
			default: $ftr = ''; break;
		}
		$ftr .= $regimen ? " AND regimen like '%".$regimen."%'" : '';


		$db->setQuery("SELECT * FROM c_UsoCfdi WHERE 1 ".$ftr);
		$usosCfdi =  $db->GetResult();
		echo 'ok[#]';
		$smarty->assign("nameId", $alterno ? 'alternativeUsoCfdi' : 'claveUsoCfdi');
		$currentUsoCfdi =  null;
		if(isset($contractInfo['contractId']))
			$currentUsoCfdi  = $alterno ? $contractInfo['alternativeUsoCfdi'] :  $contractInfo['claveUsoCfdi'];

		$smarty->assign("currentUsoCfdi", $currentUsoCfdi);
		$smarty->assign("usosCfdi", $usosCfdi);
		$smarty->display(DOC_ROOT.'/templates/forms/comp-uso-cfdi.tpl');
	break;
	case 'loadRegimen':
		$persona = $_POST['persona'] ?? false;
		$contrato = $_POST['contractId'] ?? false;
		$alterno = $_POST['alterno'] ?? false;

		$contratoInfo = [];
		if($contrato) {
			$contract->setContractId($contrato);
			$contractInfo = $contract->Info();
		}

		$ftr = "";
		switch($persona) {
			case 'Persona Moral': $ftr .=" AND tax_purpose IN(1,3) "; break;
			case 'Persona Fisica': $ftr .=" AND tax_purpose IN(2,3) ";break;
			default: $ftr = ''; break;
		}

		$db->setQuery("SELECT * FROM regimen WHERE 1 ".$ftr);
		$regimenes =  $db->GetResult();
		echo 'ok[#]';
		$smarty->assign("nameId", $alterno ? 'alternativeRegimen' : 'regimenId');
		$currentRegimen =  null;
		if(isset($contractInfo['contractId']))
			$currentRegimen  = $alterno ? $contractInfo['alternativeRegimen'] :  $contractInfo['regimenId'];

		$smarty->assign("currentRegimen", $currentRegimen);
		$smarty->assign("actionChange", $alterno ? 'loadUsoCfdiAlternativo()':  false);
		$smarty->assign("regimenes", $regimenes);
		$smarty->display(DOC_ROOT.'/templates/forms/comp-regimen.tpl');
		break;


}
?>
