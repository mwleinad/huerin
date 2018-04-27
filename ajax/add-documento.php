<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 12/02/2018
 * Time: 04:34 PM
 */
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();
switch($_POST["type"])
{
     case 'saveAddDocumento':
         $documento->setContractId($_POST["contractId"]);
         $documento->setTipoDocumentoId($_POST["tipoDocumentoId"]);
         $documento->setFile($_FILES['path']);
         if($_POST['tipoDocumentoId']==24)
             $documento->setDateExpiration($_POST["expiration"]);

         if($documento->Save()){
             $documento->setContractId($_POST["contractId"]);
             $documentos = $documento->Enumerate();

             echo "ok[#]";
             $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
             echo "[#]";
             $smarty->assign("documentos", $documentos);
             $smarty->display(DOC_ROOT."/templates/lists/documento.tpl");

         }
         else{
             echo "fail[#]";
             $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
         }
     break;
    case 'openAddDocumento':
        $tiposDocumento = $tipoDocumento->Enumerate();
        $smarty->assign("tiposDocumento", $tiposDocumento);
        $smarty->assign('contractId',$_POST['contractId']);
        $smarty->display(DOC_ROOT.'/templates/boxes/add-documento-popup.tpl');
    break;



}