<?php
    //comprobar permiso nivel uno
    $user->allowAccess(2);
    //si es cliente aunque tenga permiso en modulo cliente debe redireccionar solo al apartado de customer-only
    if($_SESSION['User']['roleId']==4){
        header('Location: '.WEB_ROOT.'/customer-only');
        exit;
    }
    /* End Session Control */
    if($_GET["tipo"]=='')
        $_GET["tipo"] = "Activos";

    switch($_GET['tipo']){
        case 'Activos':
            $_GET["tipo"] = "Activos";
            $user->allowAccess(91);
        break;
        case 'Inactivos':
            $_GET["tipo"] = "Inactivos";
            $user->allowAccess(92);
        break;
        default:
            $user->allowAccess(-1);
        break;
    }

	if($_GET["delete"] == "Inactivos"){
		$customer->DeleteInactivos();
?>
    <script language="javascript">
		alert("Los clientes inactivos han sido eliminados permanentemente");
		</script>
<?php
	}
    if($User['level']==1){
        $result = $customer->SuggestCustomerCatalogFiltrado("","subordinados",0,$_GET["tipo"],false);
        $smarty->assign("customers", $result);
    }

	
	if(isset($_SESSION["tipoMod"]))
	    unset($_SESSION["tipoMod"]);

	$personals = $personal->Enumerate();
	$smarty->assign("personals", $personals);
    
	$_SESSION["tipoMod"] = $_GET["tipo"];

	$smarty->assign("tipo", $_GET["tipo"]);
	$smarty->assign('mainMnu','contratos');

?>