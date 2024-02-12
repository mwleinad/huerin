<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();
switch($_POST["type"])
{
	case "search":
		$_POST['deep'] = $_POST['deep']==='true' ? true : false ;
		$encargados = $personal->GetIdResponsablesSubordinados($_POST);
		$filter = $_POST;
		$filter['like'] = $_POST['valur'];
		$filter['tipos'] = $_POST['tipo'];
		$filter['encargados'] = $encargados;
		$result = $customer->EnumerateAllCustomer($filter);
		$smarty->assign("customers", $result);
		$smarty->display(DOC_ROOT.'/templates/lists/customer.tpl');
	break;
	case "addCustomer":
		$data['title'] ="Agregar cliente";
		$data['form'] = "frm-customer";
		$data['nameForm'] = "addCustomerForm";
		$empleados = $personal->Enumerate();
		$smarty->assign("data", $data);
		$smarty->assign("partners", $catalogue->ListAssociated());
		$smarty->assign("clasificaciones", $catalogue->EnumerateCatalogue('tipo_clasificacion_cliente'));
		$smarty->assign("empleados", $empleados);
		$smarty->assign("tipo", $_POST["tipo"]);
		$smarty->display(DOC_ROOT.'/templates/boxes/general-popup.tpl');
	break;
	case "saveAddCustomer":
			$customer->setName($_POST['name']);
			$customer->setPhone($_POST['phone']);
			$customer->setEmail($_POST['email']);
			$customer->setNameContact($_POST['nameContact']);
			$customer->setEncargadoCuenta($_POST['encargadoCuenta']);
			$customer->setResponsableCuenta($_POST['responsableCuenta']);
			$customer->setFechaAlta($_POST['fechaAlta']);
			$customer->setPassword($_POST['password']);
			$customer->setObservacion($_POST["observacion"]);
			$customer->setNoFactura13(isset($_POST['noFactura13']) ? 'Si' : 'No');
			if((int)$_POST['is_referred'] === 1) {
				$customer->setIsReferred(1);
				$customer->setTypeReferred($_POST['type_referred']);
				if($_POST['type_referred'] === 'partner')
					$customer->setPartner($_POST['partner_id']);

				if($_POST['type_referred'] === 'otro')
					$customer->setNameReferrer($_POST['name_referrer']);

			}
			$customer->setTipoClasificacionCliente($_POST['tipo_clasificacion_cliente_id']);
			echo $customer->Save() ? 'ok[#]' : 'fail[#]';
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			echo "[#]";
			$resCustomers = $customer->Enumerate($type = "subordinado", $customerId = 0,  $_SESSION["tipoMod"]);
			$smarty->assign("customers", $resCustomers);
			$smarty->display(DOC_ROOT.'/templates/lists/customer.tpl');
		break;

	case "deleteCustomer":
			$customer->setCustomerId($_POST['customerId']);
			if($customer->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
		break;

	case "editCustomer":
			$empleados = $personal->Enumerate();
			$data['title'] ="Editar cliente";
			$data['form'] = "frm-customer";
			$data['nameForm'] = "editCustomerForm";
			$customer->setCustomerId($_POST['customerId']);
			$info = $customer->Info();

			$smarty->assign("valur", $_POST["valur"]);
			$smarty->assign("tipo", $_POST["tipo"]);
			$smarty->assign("partners", $catalogue->ListAssociated());
			$smarty->assign("clasificaciones", $catalogue->EnumerateCatalogue('tipo_clasificacion_cliente'));
			$smarty->assign("post", $info);
			$smarty->assign("data", $data);
			$smarty->display(DOC_ROOT.'/templates/boxes/general-popup.tpl');
		break;

	case "saveEditCustomer":
			$customer->setCustomerId($_POST['customerId']);
			$customer->setName($_POST['name']);
			$customer->setPhone($_POST['phone']);
			$customer->setEmail($_POST['email']);
			$customer->setNameContact($_POST['nameContact']);
			$customer->setEncargadoCuenta($_POST['encargadoCuenta']);
			$customer->setResponsableCuenta($_POST['responsableCuenta']);
			$customer->setFechaAlta($_POST['fechaAlta']);
        	$customer->setObservacion($_POST['observacion']);
			$customer->setNoFactura13(isset($_POST['noFactura13']) ? 'Si' : 'No');
			if((int)$_POST['is_referred'] === 1) {
				$customer->setIsReferred(1);
				$customer->setTypeReferred($_POST['type_referred']);
				if($_POST['type_referred'] === 'partner')
					$customer->setPartner($_POST['partner_id']);

				if($_POST['type_referred'] === 'otro')
					$customer->setNameReferrer($_POST['name_referrer']);

			}

		    $customer->setTipoClasificacionCliente($_POST['tipo_clasificacion_cliente_id']);

			$info = $customer->Info();
			$_POST['password'] != $info["password"] && $_POST["password"] != ""
			? $customer->setPassword($_POST['password'])
			: $customer->setPassword($info['password']);

			echo $customer->Edit() ? 'ok[#]' : 'fail[#]';
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
	break;
	case 'openModalBajaTemporal':
		$customer->setCustomerId($_POST['id']);
		$contratos =  $customer->getListContratos('activo');

        $_POST['typeSave'] = "doBajaTemporal";
		$_POST['title']="Baja temporal de servicios";
		$smarty->assign('contratos',$contratos);
        $smarty->assign('post',$_POST);
		$smarty->display(DOC_ROOT."/templates/boxes/down-service-from-customer-popup.tpl");
	break;
    case 'openModalReactiveTemporal':
        $customer->setCustomerId($_POST['id']);
        $contratos =  $customer->getListContratos('bajaParcial');

        $_POST['reactive']=true;
        $_POST['typeSave'] = "doReactiveTemporal";
        $_POST['title']="Reactivacion de servicios";
        $smarty->assign('contratos',$contratos);
        $smarty->assign('post',$_POST);
        $smarty->display(DOC_ROOT."/templates/boxes/down-service-from-customer-popup.tpl");
        break;
	case 'doBajaTemporal':
		  $servicio->setIdContracts($_POST['idContracts']);
		  if($servicio->doBajaTemporalMultiple('activo','bajaParcial')){

              echo "ok[#]";
              $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		  }else{
              echo "fail[#]";
              $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		  }
	break;
    case 'doReactiveTemporal':
        $servicio->setIdContracts($_POST['idContracts']);
        if($servicio->doBajaTemporalMultiple('bajaParcial','activo')){
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;

}
?>
