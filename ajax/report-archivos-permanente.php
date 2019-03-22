<?php
/** 
* report-archivos-permanente.php
*
* PHP version 5
*
* @category Desarrollo
* @package  Report-archivos-permanente.php
* @author   Daniel Lopez <desarrollos@avantika.com.mx>
* @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link     http://avantika.com.mx
**/

	require_once '../init.php';
	require_once '../config.php';
	require_once DOC_ROOT.'/libraries.php';
	
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
		  
		  	$tiposArchivos = $tipoArchivo->Enumerate();
		  	$smarty->assign('tiposArchivos', $tiposArchivos);
		
		  	$totalArchivos=count($tiposArchivos);
		  	if ($totalArchivos%2 != 0) {
				$totalArchivos+=1;
		  	}
		  
		  	$totalArchivos=(($totalArchivos/2));
		  	$smarty->assign('totalArchivos', $totalArchivos);
		  
		  	$smarty->assign('mainMnu', 'reportes');
		  
		  	$archivos = $archivo->EnumerateAll();
		
		  	$contracts = array();
		  	//filtro
            include_once(DOC_ROOT.'/ajax/filter.php');
		 
		  	foreach ($contracts as $key => $value) {
				if (preg_match("/TENGO/i", $contracts[$key]['claveFiel'])) {
				  $contracts[$key]['claveFiel']="";
				}
				if (preg_match("/TENGO/i", $contracts[$key]['claveCiec'])) {
				  $contracts[$key]['claveCiec']="";
				}
				if (preg_match("/(TENGO|APLICA)/i", $contracts[$key]['claveIdse'])) {
				  $contracts[$key]['claveIdse']="";
				}
				if (preg_match("/(TENGO|APLICA)/i", $contracts[$key]['claveIsn'])) {
				  $contracts[$key]['claveIsn']="";
				}
				
				foreach ($archivos as $keyArc => $valueArc) {
				  if ($contracts[$key]['contractId']==$archivos[$keyArc]['contractId']) {
					$contracts[$key]['archivos'][]=$archivos[$keyArc];
				  }
				}

		  	}

			$personalOrdenado = $personal->ArrayOrdenadoPersonal();

			$sortedArray = array();
			/*foreach($personalOrdenado as $personalKey => $personalValue)
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
			}*/

	  		$smarty->assign('contracts', $contracts);
	  
		  	$values['nombre'] = $_POST['rfc'];
		  	$values['facturador'] = $_POST['facturador'];
	
		  	$smarty->assign("DOC_ROOT", DOC_ROOT);
		  	$smarty->display(DOC_ROOT.'/templates/lists/report-archivos-permanente.tpl');
			
		break;        
		
	}
	
?>
