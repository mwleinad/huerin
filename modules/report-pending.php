<?php
$user->allowAccess(217);
$user->allowAccess(221);
/* End Session Control */

if($_SESSION["search"]["year"])
{
    $year = $_SESSION["search"]["year"];
}
else
{

    $year = date("Y");
}
$result = $change->Enumerate();
$smarty->assign("result",$result);
$smarty->assign("year", $year);
$smarty->assign('mainMnu','configuracion');

?>