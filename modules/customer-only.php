<?php

/* Start Session Control - Don't Remove This */
//$user->allowAccess('customer');
/* End Session Control */
$result = $customer->SuggestCustomerCatalog("", $type = "subordinado", $customerId = $_SESSION["User"]['userId'], "Activos");
$smarty->assign("customers", $result);

if(isset($_SESSION["tipoMod"]))
    unset($_SESSION["tipoMod"]);

$personals = $personal->Enumerate();
$smarty->assign("personals", $personals);

$_SESSION["tipoMod"] = $_GET["tipo"];

$smarty->assign("tipo", $_GET["tipo"]);
$smarty->assign('mainMnu','customer-only');

?>