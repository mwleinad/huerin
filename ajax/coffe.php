<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case "openAddCoffePopup":
		$smarty->display(DOC_ROOT."/templates/boxes/add-coffe-popup.tpl");
     break;
	case 'addPlatillo':
		if($_POST['name']=="")
		{
			$util->setError('','error','Nombre de platillo obligatorio.');
			$util->PrintErrors();
			echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			return false;
		}
		if(!is_array($_SESSION['platillos']))
			$_SESSION['platillos'] =  [];

        end($_SESSION["platillos"]);
        $platillo = key($_SESSION["platillos"]) + 1;
        $_SESSION["platillos"][$platillo] = $_POST['name'];
        $smarty->assign('platillos',$_SESSION['platillos']);
        echo "ok[#]";
        $smarty->display(DOC_ROOT."/templates/lists/list-menu.tpl");

	break;
    case 'deletePlatillo':
    	$key = $_POST['id'];
        unset($_SESSION['platillos'][$key]);
        $smarty->assign('platillos',$_SESSION['platillos']);
        echo "ok[#]";
        $smarty->display(DOC_ROOT."/templates/lists/list-menu.tpl");

        break;
	case 'saveMenu':
        if(!is_array($_SESSION['platillos'])||count($_SESSION['platillos'])<=0)
        {
            $util->setError('','error','Es necesario agrear al menos un  platillo al menu.');
            $util->PrintErrors();
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            return false;
        }
        $coffe = new Coffe();
        if($coffe->SaveMenu(true))
		{
			$menus =  $coffe->Enumerate();
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign('menus',$menus);
            $smarty->display(DOC_ROOT.'/templates/lists/coffe.tpl');
		}else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		}
    break;
    case 'deleteMenu':
        $coffe = new Coffe();
        $coffe->setId($_POST['id']);
        if($coffe->DeleteMenu())
        {
            $menus =  $coffe->Enumerate();
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign('menus',$menus);
            $smarty->display(DOC_ROOT.'/templates/lists/coffe.tpl');
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }

        break;
}
?>
