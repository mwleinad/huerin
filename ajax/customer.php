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
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->assign("customers", $result);
		$smarty->display(DOC_ROOT.'/templates/lists/customer.tpl');
	break;
	case "addCustomer":
		$empleados = $personal->Enumerate();
		$smarty->assign("empleados", $empleados);
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->display(DOC_ROOT.'/templates/boxes/add-customer-popup.tpl');
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
						
			if($_POST['active'])
				$customer->setActive(1);
			else
				$customer->setActive(0);
			
			if(!$customer->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resCustomers = $customer->Enumerate($type = "subordinado", $customerId = 0,  $_SESSION["tipoMod"]);
				//$resCustomers = $customer->Search("subordinado", $_SESSION["tipoMod"]);
				
				$smarty->assign("customers", $resCustomers);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/customer.tpl');
			}
			
		break;
		
	case "deleteCustomer":
			$customer->setCustomerId($_POST['customerId']);
			if($customer->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				/*$resCustomers = $customer->Search("subordinado", $_SESSION["tipoMod"]);
//				$resCustomers = $customer->Enumerate();
				$customers = $resCustomers;
				
				$smarty->assign("customers", $customers);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/customer.tpl');*/
			}
		break;
		
	case "editCustomer":
			$empleados = $personal->Enumerate();			
			$smarty->assign("empleados", $empleados);
			$smarty->assign("valur", $_POST["valur"]);
			$smarty->assign("tipo", $_POST["tipo"]);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$customer->setCustomerId($_POST['customerId']);
			$info = $customer->Info();
			$smarty->assign("post", $info);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-customer-popup.tpl');
		
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
			
			$info = $customer->Info();
			
			if($_POST['password'] != $info["password"] && $_POST["password"] != "")
			{
				$customer->setPassword($_POST['password']);
			}
			else
			{
				$customer->setPassword($info['password']);
			}
			
			
			if($_POST['active'])
				$customer->setActive(1);
			else
				$customer->setActive(0);

			if($_POST['noFactura13'] == "Si")
				$customer->setNoFactura13("Si");
			else
				$customer->setNoFactura13("No");
			
			if(!$customer->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				/*echo "[#]";
				$resCustomers = $customer->Search("subordinado", $_POST["tipo"]);

//				$resCustomers = $customer->Enumerate();
				$customers = $resCustomers;
				$smarty->assign("customers", $customers);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/customer.tpl');*/
			}
			
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
