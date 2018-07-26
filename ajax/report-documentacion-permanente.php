<?php

	include_once('../init.php');
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	session_start();	
	
	switch($_POST["type"])
	{
		case "search":
				$formValues['cliente']=$_POST["rfc"];
				$formValues['razonSocial']=$_POST["rfc2"];
				$formValues['departamentoId']=$_POST["departamentoId"];
				$formValues['respCuenta'] = $_POST['responsableCuenta'];
				$formValues['facturador'] = $_POST['facturador'];
				$formValues['subordinados'] = $_POST['subordinados'];
						
				$tiposDocumentos = $tipoDocumento->Enumerate();
				$smarty->assign('tiposDocumentos',$tiposDocumentos);
				$totalDocumentos=count($tiposDocumentos);

				if($totalDocumentos%2 != 0)
					{$totalDocumentos+=1;}
				
				$totalDocumentos=($totalDocumentos/2);
				$smarty->assign('totalDocumentos',$totalDocumentos);			
				$smarty->assign('mainMnu','reportes');
				
				$documentos = $documento->EnumerateAll();
				$contracts = array();

				include_once(DOC_ROOT.'/ajax/filter.php');
				
				foreach($contracts as $key => $value)
				{
					foreach($documentos as $keyDocto => $valueDocto)
					{
						if($contracts[$key]['contractId']==$documentos[$keyDocto]['contractId'])
						{
							$contracts[$key]['documentos'][]=$documentos[$keyDocto];
						}
					}
				}

				$personalOrdenado = $personal->ArrayOrdenadoPersonal();

				$sortedArray = array();
				foreach($personalOrdenado as $personalKey => $personalValue)
				{
					foreach($contracts as $keyCleaned => $cleanedArrayValue)
					{
						$cleanedArrayValue["responsableCuenta"];
						if($personalValue["personalId"] == $cleanedArrayValue["responsableCuenta"])
						{
							$sortedArray[] = $cleanedArrayValue;
							unset($cleanedArrayValue[$keyCleaned]);
						}
					}
				}

				$smarty->assign('contracts', $sortedArray);

				$values['nombre'] = $_POST['rfc'];
				$values['facturador'] = $_POST['facturador'];
	
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/report-documentacion-permanente.tpl');
				
			break;				
	}
	
?>
