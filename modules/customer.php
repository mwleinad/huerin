<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('customer');	
	/* End Session Control */
	if(!$_GET["tipo"])
	{
		$_GET["tipo"] = "Activos";
	}

	if($_GET["delete"] == "Inactivos"){
		$customer->DeleteInactivos();
?>
    <script language="javascript">
		alert("Los clientes inactivos han sido eliminados permanentemente");
		</script>
<?php
	}

  $result = $customer->SuggestCustomerCatalog("", $type = "subordinado", $customerId = 0, $_GET["tipo"]);
  $smarty->assign("customers", $result);  	 
	
	if(isset($_SESSION["tipoMod"]))
	    unset($_SESSION["tipoMod"]);

	$personals = $personal->Enumerate();
	$smarty->assign("personals", $personals);
    
	$_SESSION["tipoMod"] = $_GET["tipo"];

	$smarty->assign("tipo", $_GET["tipo"]);
	$smarty->assign('mainMnu','contratos');

?>