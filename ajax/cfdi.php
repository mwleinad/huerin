<?php

	include_once('../init.php');
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');

	switch($_POST['type']){

		case 'buscar':

				echo 'ok[#]';

				foreach($_POST as $key => $val)
					$values[$key] = $val;

				$comprobantes = array();
				$comprobantes = $cfdi->Search($values);

				$smarty->assign('comprobantes',$comprobantes);

				echo '[#]';
				$smarty->assign('DOC_ROOT', DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/cfdi.tpl');

			break;


	}//switch

?>
