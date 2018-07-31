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

    /* Star Session Control Modules*/
    $user->allowAccess(7);  //level 1
    $user->allowAccess(159);//level 2
    /* end Session Control Modules*/
    $rol->setRolId($User['roleId']);
    $unlimited = $rol->ValidatePrivilegiosRol(array('gerente','supervisor','contador','auxiliar'),array('Gerente de Juridico'));

  	$personals = $personal->Enumerate();
	$departamentos = $departamentos->Enumerate();

    $smarty->assign("unlimited", $unlimited);
	$smarty->assign("personals", $personals);
	$smarty->assign("departamentos", $departamentos);
  	$smarty->assign('mainMnu', 'reportes');
	
?>