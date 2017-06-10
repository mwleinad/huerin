<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('contract-subcategory');	
	/* End Session Control */
	
	$contCatId = $_GET['contCatId'];
	
	$_SESSION['idContCat'] = $contCatId;
		
	$contCat->setContCatId($contCatId);
	$catName = $contCat->GetNameById();
	
	$contSubcat->setContCatId($contCatId);
	$subcategories = $contSubcat->Enumerate();
	
	$smarty->assign('subcategories', $subcategories);
	$smarty->assign('catName',$catName);
	$smarty->assign('mainMnu','catalogos');
	
?>