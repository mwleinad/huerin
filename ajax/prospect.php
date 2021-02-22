<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');

switch ($_POST['type']) {
    case "openAddProspect":
        $data['title'] = "Agregar prospecto";
        $data["form"] = "frm-prospect";
        $smarty->assign("data", $data);
        $smarty->display(DOC_ROOT . "/templates/boxes/general-popup.tpl");
        break;
    case "openEditProspect":
        $data['title'] = "Editar prospecto";
        $data["form"] = "frm-prospect";
        $prospect->setId($_POST['id']);
        $smarty->assign("post", $prospect->info());
        $smarty->assign("data", $data);
        $smarty->display(DOC_ROOT . "/templates/boxes/general-popup.tpl");
        break;

    case "saveProspect":
        $prospect->setName($_POST['name']);
        $prospect->setPhone($_POST['phone']);
        $prospect->setEmail($_POST['email']);
        $prospect->setObservation($_POST['observation']);
        echo $prospect->save() ? "ok" : "fail";
        echo "[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $smarty->assign('results', $prospect->enumerate());
        $smarty->display(DOC_ROOT . '/templates/lists/prospect.tpl');
        break;
    case "updateProspect":
        $prospect->setId($_POST['id']);
        $prospect->setName($_POST['name']);
        $prospect->setPhone($_POST['phone']);
        $prospect->setEmail($_POST['email']);
        $prospect->setObservation($_POST['observation']);
        echo $prospect->update() ? "ok":"fail";
        echo "[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $smarty->assign('results', $prospect->enumerate());
        $smarty->display(DOC_ROOT . '/templates/lists/prospect.tpl');
        break;
}
