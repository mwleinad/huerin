<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(161);//level 2
/* end Session Control Modules*/


if($_SESSION["search"]["year"])
{
    $year = $_SESSION["search"]["year"];
}
else
{
    $year = date("Y");
}
$smarty->assign("year", $year);
$personals = $personal->Enumerate();
$departamentos = $departamentos->Enumerate();

$smarty->assign("personals", $personals);
$smarty->assign("departamentos", $departamentos);
$smarty->assign('mainMnu', 'reportes');

?>