<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();

switch($_POST["type"]){
    case 'openModalAddEmisor':
        $data['title'] =  "Agregar emisor";
        $data["form"] = "frm-emisor";
        $smarty->assign("data",$data);
        $smarty->assign("tiposRegimen",$regimen->ListTiposRegimen());
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
        break;
    case 'saveEmisor':
        $rfc->setEmpresaId($_SESSION["empresaId"]);
        $rfc->setRazonSocial($_POST["razonSocial"]);
        $rfc->setRfc($_POST["rfc"]);
        $rfc->setRegimenFiscal($_POST["regimenFiscal"]);
        $rfc->setCalle($_POST["calle"]);
        $rfc->setNoExt($_POST["noExt"]);
        $rfc->setNoInt($_POST["noInt"]);
        $rfc->setColonia($_POST["colonia"]);
        $rfc->setCp($_POST["cp"]);
        $rfc->setCiudad($_POST["ciudad"]);
        $rfc->setEstado($_POST["estado"]);
        $rfc->setPais($_POST["pais"]);
        $rfc->setClaveFacturador($_POST["claveFacturador"]);
        if($rfc->AddRfc()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("results", $rfc->EnumerateRfc());
            $smarty->display(DOC_ROOT . "/templates/lists/emisores.tpl");
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        }
        break;
    case 'openEditEmisor':
        $data['title'] =  "Editar emisor";
        $data["form"] = "frm-emisor";
        $smarty->assign("data",$data);

        $rfc->setRfcId($_POST["id"]);
        $info = $rfc->InfoRfc();
        $smarty->assign("post",$info);
        $smarty->assign("tiposRegimen",$regimen->ListTiposRegimen());
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
        break;
    case 'updateEmisor':
        $rfc->setEmpresaId($_SESSION["empresaId"]);
        $rfc->setRfcId($_POST["rfcId"]);
        $rfc->setRazonSocial($_POST["razonSocial"]);
        $rfc->setRfc($_POST["rfc"]);
        $rfc->setRegimenFiscal($_POST["regimenFiscal"]);
        $rfc->setCalle($_POST["calle"]);
        $rfc->setNoExt($_POST["noExt"]);
        $rfc->setNoInt($_POST["noInt"]);
        $rfc->setColonia($_POST["colonia"]);
        $rfc->setCp($_POST["cp"]);
        $rfc->setCiudad($_POST["ciudad"]);
        $rfc->setEstado($_POST["estado"]);
        $rfc->setPais($_POST["pais"]);
        if($rfc->EditRfc()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("results", $rfc->EnumerateRfc());
            $smarty->display(DOC_ROOT . "/templates/lists/emisores.tpl");
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        }
    break;
    case 'openModalCertificate':

        $data["form"] = "frm-emisor-certificate";
        $smarty->assign("data",$data);
        $data['title'] =  "";

        $rfc->setRfcId($_POST["id"]);
        $info = $rfc->InfoRfc();
        $smarty->assign("post",$info);
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
        break;
    case 'processCertificate':
        $rfc->setRfcId($_POST['rfcId']);
        $rfc->setEmpresaId($_POST['empresaId']);
        $rfc->validateFileCertificado();
        $rfc->validateFileKey();
        $rfc->setPassword($_POST['pass_llave']);
        if($rfc->processCertificate()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("results", $rfc->EnumerateRfc());
            $smarty->display(DOC_ROOT . "/templates/lists/emisores.tpl");
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        }
        break;
    case 'deleteEmisor':
        $rfc->setRfcId($_POST["id"]);
        if($rfc->DeleteRfc()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("results", $rfc->EnumerateRfc());
            $smarty->display(DOC_ROOT . "/templates/lists/emisores.tpl");
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        }
        break;
}