<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

//session_start();

switch($_POST["type"])
{
	case 'doLogin':
		$username = strip_tags($_POST['username']);
		$passwd = strip_tags($_POST['passwd']);
		
		$user->setUsername($username);
		$user->setPassword($passwd);
		
		//Eliminamos la sesiones activas por otros usuarios
		
		$_SESSION['User'] = array();
		unset($_SESSION['User']);
		$_SESSION['Usr'] = array();
		unset($_SESSION['Usr']);
		
		if($user->doLogin()){
			//echo session_id();
//			print_r($_SESSION);
//			exit;
			echo "ok[#]";
		}else{
			echo "fail[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		}
		
		break;
}

?>
