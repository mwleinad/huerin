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

  	/* Start Session Control - Don't Remove This */
  	$user->allowAccess("report-ingresos");
  	/* End Session Control */
  
  	$personals = $personal->Enumerate();
	$departamentos = $departamentos->Enumerate();
	
	$smarty->assign("personals", $personals);
	$smarty->assign("departamentos", $departamentos);
  	$smarty->assign('mainMnu', 'reportes');
	
?>