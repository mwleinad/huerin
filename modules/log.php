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

  /* Start Session Control - Don't Remove This */
  $user->allowAccess("log");
  $filasLog=$log->GetLog();
  $smarty->assign('filasLog', $filasLog);
  /* End Session Control */
  $smarty->assign('mainMnu', 'reportes');
  
?>