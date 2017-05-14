<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess();	
	/* End Session Control */
			
	//Obtenemos los Documentos Basicos
	$resDBasic = $docBasic->Enumerate();
		
	$docsH = array();
	
	foreach($resDBasic as $val){
		
		$card = $val;
		
		$sql = 'SELECT 
					fecha, fechaRec, contractId 
				FROM 
					contract_docbasic 
				WHERE 
					aplica = "1"				
				AND
					docBasicId = "'.$val['docBasicId'].'"';
		$util->DB()->setQuery($sql);
		$row = $util->DB()->GetRow();
		
		if($row['fecha']){
			$card['fechaEnt'] = date('d-m-Y',strtotime($row['fecha']));
			$fecha = $row['fecha'];
		}
		
		if($row['fechaRec']){
			$card['fechaRec'] = date('d-m-Y',strtotime($row['fechaRec']));
			$fecha = $row['fechaRec'];
		}
				
		if($fecha){
			$mes = $util->GetMonthByKey(date('n',strtotime($fecha)));
			$mes = substr($mes,0,3);
			$card['mes'] = $mes.' '.date('y');
		}
		
		if($row['fechaRec'])
			$status = 'Entregado';
		else		
			$status = $util->GetStatusByDate($fecha);	
		
		$contract->setContractId($row['contractId']);			
		$card['proyecto'] = $contract->GetNameById();
		
		$card['fecha'] = $fecha;					
		$card['seccion'] = 'Control de Doc.';
		$card['section'] = 'B';
		$card['status'] = $status;
		
		if($row)
			$docsH[] = $card;
	}
	
	$contract->setContractId($contractId);
	$info = $contract->Info();
	
	//Obtenemos los Documentos Generales
	$resDGral = $docGral->Enumerate();
	
	foreach($resDGral as $val){
		
		$card = $val;
		
		$sql = 'SELECT 
					fecha, fechaRec, cartaCump, contractId
				FROM 
					contract_docgral 
				WHERE
					aplica = "1"				
				AND
					docGralId = "'.$val['docGralId'].'"';
		$util->DB()->setQuery($sql);
		$row = $util->DB()->GetRow();
		
		
		//Checamos si existe Prorroga
		if($row){
			$fechaProrroga = $contract->getLastProrroga($row['contractId'], $val['docGralId']);
			if($fechaProrroga){
				$row['fecha'] = $fechaProrroga;				
			}		
		}
		
		if($row['fecha']){
			$card['fechaEnt'] = date('d-m-Y',strtotime($row['fecha']));
			$fecha = $row['fecha'];
		}
		
		if($row['fechaRec']){
			$card['fechaRec'] = date('d-m-Y',strtotime($row['fechaRec']));
			$fecha = $row['fechaRec'];
		}
				
		if($fecha){
			$mes = $util->GetMonthByKey(date('n',strtotime($fecha)));
			$mes = substr($mes,0,3);
			$card['mes'] = $mes.' '.date('y');
		}
		
		if($row['fechaRec'])
			$status = 'Entregado';
		else		
			$status = $util->GetStatusByDate($fecha);	
		
		$contract->setContractId($row['contractId']);			
		$card['proyecto'] = $contract->GetNameById();
		
		$card['fecha'] = $fecha;		
		$card['cartaCump'] = $row['cartaCump'];	
		$card['seccion'] = 'Doc. Obligaciones';
		$card['section'] = 'O';
		$card['status'] = $status;
		
		if($row)			
			$docsH[] = $card;
	}
		
	//Obtenemos los Documentos Sellado
	$resDocs = $docSellado->Enumerate();
	$resDSellado = $util->EncodeResult($resDocs);
	
	foreach($resDSellado as $val){
		
		$card = $val;
		
		$fecha = $val['fecha'];
		
		$sql = 'SELECT 
					fecha, fechaRec, enviado, contractId
				FROM 
					contract_docsellado 
				WHERE 					
					docSelladoId = "'.$val['docSelladoId'].'"';
		$util->DB()->setQuery($sql);
		$row = $util->DB()->GetRow();
						
		if($row['fecha']){
			$card['fechaEnt'] = date('d-m-Y',strtotime($row['fecha']));
			$fecha = $row['fecha'];
		}
		
		if($row['fechaRec']){
			$card['fechaRec'] = date('d-m-Y',strtotime($row['fechaRec']));
			$fecha = $row['fechaRec'];
		}
				
		if($fecha){
			$mes = $util->GetMonthByKey(date('n',strtotime($fecha)));
			$mes = substr($mes,0,3);
			$card['mes'] = $mes.' '.date('y');
		}
		
		if($row['fechaRec'])
			$status = 'Entregado';
		else		
			$status = $util->GetStatusByDate($fecha);	
		
		$contract->setContractId($row['contractId']);			
		$card['proyecto'] = $contract->GetNameById();
		
		$card['fecha'] = $fecha;
		$card['seccion'] = 'Doc. Sellada y Rubricada';
		$card['section'] = 'S';
		$card['status'] = $status;
		
		$docsH[] = $card;
	}
	
	$docsH = $util->orderMultiDimensionalArray($docsH,'fecha');
	
	
	$docs = array();
	$entrar = true;
	$inf = array();
	foreach($docsH as $key => $res){
		
		if($entrar){
			$fecha = $res['fecha'];	
			$entrar = false;
		}
				
		$section = $res['section'];
		
		$inf[$section] = $res;
		
		if($fecha != $res['fecha'] || $res['fecha'] == ''){
			$fecha = $res['fecha'];
			$docs[$key] = $inf;
			$inf = array();
		}
				
	}//foreach
		
	$smarty->assign("info", $info);		
	$smarty->assign("docs", $docs);
	$smarty->assign('mainMnu','reportes');

?>