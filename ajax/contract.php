<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

$User = $_SESSION['User'];
$smarty->assign('User',$User);

switch($_POST["type"])
{
	case "addContract": 
			
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-contract-popup.tpl');
		
		break;	
		
	case "saveAddContract":
			
			$contract->setName($_POST['name']);			
												
			if(!$contract->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resContracts = $contract->Enumerate();
				
				$contracts = $util->EncodeResult($resContracts);

				$empleados = $personal->Enumerate();			
				$smarty->assign("empleados", $empleados);
				print_r($empleados);
				
				$smarty->assign("contracts", $contracts);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/contract.tpl');
			}
			
		break;
		
	case "deleteContract":
			$contract->setContractId($_POST['contractId']);
			if($contract->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				
				if($_POST["customer"])
				{
					$resContracts = $contract->Enumerate($_POST["customer"]);
				}
				else
				{
					$resContracts = $contract->Enumerate();
				}
	
				$contracts = array();
				foreach($resContracts as $key => $val){
					
					$card = $val;
					
					$card['name'] = utf8_encode($val['name']);
					
					$contCat->setContCatId($val['contCatId']);
					$card['tipo'] = $contCat->GetNameById();
					
					$card['status'] = ucfirst($card['status']);
					
					$contracts[$key] = $card;	
					
				}
	
				
				$smarty->assign("contracts", $contracts);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/contract.tpl');
			}
			
		break;
		
	case "editContract":	 
			
			$contract->setContractId($_POST['contractId']);
			$myContract = $contract->Info();
			
			$info = $util->EncodeRow($myContract);
			
			$smarty->assign("post", $info);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-contract-popup.tpl');
		
		break;
		
	case "saveEditContract":
			
			$contract->setContractId($_POST['contractId']);
			$contract->setName($_POST['name']);			
						
			if(!$contract->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resContracts = $contract->Enumerate();
				$contracts = $util->EncodeResult($resContracts);
				
				$smarty->assign("contracts", $contracts);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/contract.tpl');
			}
			
		break;
	
	case 'doSearch':
			
			echo 'ok[#]';
			
			$sql = '';
			
			if($_POST['name'])
				$sql .= ' AND name LIKE "%'.$_POST['name'].'%"';
			if($_POST['folio'])
				$sql .= ' AND folio = "'.$_POST['folio'].'"';
			if($_POST['contCatId'])
				$sql .= ' AND contCatId = "'.$_POST['contCatId'].'"';
			if($_POST['status'])
				$sql .= ' AND status = "'.$_POST['status'].'"';
								
			$resContracts = $contract->Search($sql);

			$contracts = array();
			foreach($resContracts as $key => $val){
				
				$card = $val;
				
				$card['name'] = utf8_encode($val['name']);
				
				$contCat->setContCatId($val['contCatId']);
				$card['tipo'] = $contCat->GetNameById();
				
				$card['status'] = ucfirst($card['status']);
				
				$contracts[$key] = $card;	
				
			}
			
			$smarty->assign("contracts", $contracts);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/contract.tpl');
			
		break;
	case 'openModalTransfer':
		$clientes = $customer->getListCustomer(1);
        $smarty->assign("clientes", $clientes);
        $smarty->assign("post", $_POST);
        $smarty->display(DOC_ROOT.'/templates/boxes/transfer-contract-popup.tpl');
	break;
	case 'doTransferContract':
		$contract->setCustomerId($_POST['customerId']);
		$contract->setContractId($_POST['contractId']);
		if($contract->TrasnferContract()){
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		}else{
			echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');

		}
	break;
	case 'doPermiso':
		$permiso = new Permiso;

		$permiso->setContractId($_POST['contractId']);
		if($permiso->doPermiso()){
            echo "ok[#]";
            $util->setError(0,'complete','Permisos actualizados.');
            $util->PrintErrors();
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		}else{
            echo "fail[#]";
            $util->setError(0,'error','Error al actualizar permisos.');
            $util->PrintErrors();
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		}
	break;
		
}
?>
