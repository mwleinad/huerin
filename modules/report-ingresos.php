<?php
/**
* report-ingresos.php
*
* PHP version 5
*
* @category Desarrollo
* @package  Report-ingresos.php
* @author   Diego Damian <diego@avantika.com.mx>
* @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link     http://avantika.com.mx
**/

    /* Star Session Control Modules*/
    $user->allowAccess(7);  //level 1
    $user->allowAccess(161);//level 2
    /* end Session Control Modules*/

  	$personals = $personal->Enumerate();
	$departamentos = $departamentos->Enumerate();
	$smarty->assign("year", date('Y'));

	$smarty->assign("personals", $personals);
	$smarty->assign("departamentos", $departamentos);
  	$smarty->assign('mainMnu', 'reportes');
?>
