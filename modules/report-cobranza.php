<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess();	
	/* End Session Control */
	
	
	//Obtenemos los Tipos de Contrato
	$categories = $contCat->Enumerate();
	
	$resContracts = $contract->Enumerate();
				
	$contracts = array();
	foreach($resContracts as $key => $val){
		
		$card = $val;
		
		$customer->setCustomerId($val['customerId']);
		$card['customer'] = $customer->GetNameById();
		
		$contCat->setContCatId($val['contCatId']);
		$card['tipo'] = $contCat->GetNameById();
		
		$card['status'] = ucfirst($card['status']);
		
		$contract->setContractId($val['contractId']);
		$card['stOblig'] = $contract->GetStatusOblig();
		
		$contracts[$key] = $card;	
		
	}
	
	$totalRegs = count($contracts);
	
	$smarty->assign("categories", $categories);
	$smarty->assign("totalRegs", $totalRegs);
	$smarty->assign("contracts", $contracts);
	$smarty->assign('mainMnu','reportes');

?>