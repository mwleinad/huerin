<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 08/08/2018
 * Time: 01:14 PM
 */
$user->allowAccess(7);  //level 1
$user->allowAccess(208);//level 2
/* end Session Control Modules*/
$month = date("m");
$year = date("Y");
$empresas = $empresa->GetListEmpresas();
$smarty->assign("empresas", $empresas);
$smarty->assign("month", $month);
$smarty->assign("year", $year);
$smarty->assign('mainMnu','reportes');
