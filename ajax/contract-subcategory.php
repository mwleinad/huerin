<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

switch($_POST["type"])
{
	case "addSubcategory": 
				
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->display(DOC_ROOT.'/templates/boxes/add-contract-subcategory-popup.tpl');
				
		break;	

	case "editSubcategory":
			
		$contSubcat->setContSubcatId($_POST['id']);
		$info = $contSubcat->Info();
						
		$smarty->assign("info", $info);				
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->display(DOC_ROOT.'/templates/boxes/edit-contract-subcategory-popup.tpl');
		
		break;
		
	case "saveAddSubcategory":				
		
		$contCatId = $_SESSION['idContCat'];
		
		$contSubcat->setContCatId($contCatId);
		$contSubcat->setName($_POST['name']);
		
		if($_POST['active'])
			$contSubcat->setActive(1);
		else
			$contSubcat->setActive(0);
		
		$contSubcatId = $contSubcat->Save();
		
		if(!$contSubcatId)
		{
			echo "fail[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		}
		else
		{
						
			echo "ok[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			echo "[#]";
			$contSubcat->setActive(0);
			$result = $contSubcat->Enumerate();
			$subcategories = $util->EncodeResult($result);
			$smarty->assign("subcategories", $subcategories);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/contract-subcategory.tpl');
		}		
		
		break;
		
	case "saveEditSubcategory":	 	
				
		$contSubcat->setContSubcatId($_POST['id']);
		$contSubcat->setName($_POST['name']);
		
		$categoryId = $contSubcat->GetContCatId();
		$contSubcat->setContCatId($categoryId);
						
		if($_POST['active'])
			$contSubcat->setActive(1);
		else
			$contSubcat->setActive(0);
			
		if(!$contSubcat->Update())
		{
			echo "fail[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		}
		else
		{
			
			echo "ok[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			echo "[#]";
			$contSubcat->setActive(0);
			$result = $contSubcat->Enumerate();
			$subcategories = $util->EncodeResult($result);
			$smarty->assign("subcategories", $subcategories);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/contract-subcategory.tpl');
		}
			
		break;
	
	case 'deleteSubcategory':
				
		$contSubcat->setContSubcatId($_POST['id']);
		$categoryId = $contSubcat->GetContCatId();	
		$contSubcat->setContCatId($categoryId);
						
		if(!$contSubcat->Delete())
		{
			echo "fail[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		}
		else
		{
			echo "ok[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			echo "[#]";
			$contSubcat->setActive(0);
			$result = $contSubcat->Enumerate();
			$subcategories = $util->EncodeResult($result);
			$smarty->assign("subcategories", $subcategories);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/contract-subcategory.tpl');
		}
			
		break;
}

?>
