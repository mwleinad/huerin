<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case "search":
			if(!$_POST["responsableCuenta"])
			{
				$page = "report-servicio";
			}
			
			$personals = $personal->Enumerate();
			//$subordinados = $filtro->Subordinados($User["userId"], true);
			//print_r($subordinados);
			if($User['tipoPersonal'] == 'Supervisor' && $_POST["responsableCuenta"] == 0){
				$personals = $personal->Enumerate();
				//echo count($personals);
				$result = array();
				foreach($personals as $res){
					
					$_POST['responsableCuenta'] = $res['personalId'];
					
					$User["userId"] = $_POST["responsableCuenta"];
								
					$personal->setPersonalId($_POST["responsableCuenta"]);
					$myUser = $personal->Info();
                    $rol->setTitulo($myUser['tipoPersonal']);
                    $roleId = $rol->GetIdByName();
                    if($roleId<=0){
                        $rol->setRolId($myUser['roleId']);
                        $row = $rol->Info();
                        $roleId=$row['rolId'];
                    }
					
					if($_POST["responsableCuenta"]){
						$User["roleId"] = $roleId;
						$User["departamentoId"] = $myUser["departamentoId"];
					} 
				
					$clientes = $customer->SuggestCustomerCatalog($_POST["valur"], $_POST["subor"], $customerId = 0, $_POST["tipo"]); 			
					$result = array_merge($result, $clientes);
					//dd($result);
					
				}//foreach
				//dd($result);
				
			}else{
				//$User["userId"] = $_POST["responsableCuenta"];
				//cambio 3/13/2016
				if(!$_POST["responsableCuenta"])
				{
					$_POST["responsableCuenta"] = $_SESSION["User"]["userId"];
					$_POST["subor"] = "subordinado";
				}
				
				$User["userId"] = $_POST["responsableCuenta"];
				$personal->setPersonalId($_POST["responsableCuenta"]);
				$myUser = $personal->Info();
                $rol->setTitulo($myUser['tipoPersonal']);
                $roleId = $rol->GetIdByName();
                if($roleId<=0){
                    $rol->setRolId($myUser['roleId']);
                    $row = $rol->Info();
                    $roleId=$row['rolId'];
                }
                if($User['tipoPersonal']=='Admin')
                    $roleId = 1;

				if($_POST["responsableCuenta"]){
					$User["roleId"] = $roleId;
					$User["departamentoId"] = $myUser["departamentoId"];
				} 
				
				$result = $customer->SuggestCustomerCatalog($_POST["valur"], $_POST["subor"], $customerId = 0, $_POST["tipo"]);
				
			}//else
			 
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
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resCustomers = $customer->Search("subordinado", $_SESSION["tipoMod"]);
//				$resCustomers = $customer->Enumerate();
				$customers = $resCustomers;
				
				$smarty->assign("customers", $customers);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/customer.tpl');
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
				echo "[#]";
				$resCustomers = $customer->Search("subordinado", $_POST["tipo"]);

//				$resCustomers = $customer->Enumerate();
				$customers = $resCustomers;
				
				$smarty->assign("customers", $customers);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/customer.tpl');
			}
			
		break;
	case 'openModalBajaTemporal':
		$customer->setCustomerId($_POST['id']);
		$contratos =  $customer->getListContratos();

		$smarty->assign('contratos',$contratos);
        $smarty->assign('post',$_POST);
		$smarty->display(DOC_ROOT."/templates/boxes/down-service-from-customer-popup.tpl");
	break;
	case 'doBajaTemporal':
		  $servicio->setContractId($_POST['contractId'],true);
          $servicio->setLastDateWorkflow($_POST['lastDateWorkflow']);
		  if($servicio->doBajaTemporalServicesByContrato()){

              echo "ok[#]";
              $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		  }else{
              echo "fail[#]";
              $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		  }
	break;
		
}
?>
