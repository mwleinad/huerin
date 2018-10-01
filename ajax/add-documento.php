<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();
switch($_POST["type"])
{
    case 'openModalFile':
        switch($_POST['tipo']){
            case 'requerimiento':
                $tiposFiles =$tipoRequerimiento->Enumerate();
            break;
            case 'archivo':
                $tiposFiles =$tipoArchivo->Enumerate();
             break;
            case 'documento':
                $tiposFiles =$tipoDocumento->Enumerate();
            break;
        }
        $smarty->assign("tiposFiles", $tiposFiles);
        $smarty->assign('post',$_POST);
        $smarty->display(DOC_ROOT.'/templates/boxes/add-file-popup.tpl');
        $template = $smarty->fetch(DOC_ROOT.'/templates/forms/frm-dropzone.tpl');
        echo "[#]";
        echo $template;
    break;
    case 'saveFile':
        //tratar el archivo segun el tipo
        $dropzone->setFileId($_POST['fileId']);
        $dropzone->setRelacionId($_POST['id']);
        $dropzone->setFieldRelacion('contractId');
        //setear los campos segun el tipo de archivo que se este subiendo
        switch ($_POST['tipoFile']){
            case 'requerimiento':
                $dropzone->setTable('requerimiento');
                $dropzone->setKeyTable('requerimientoId');
                $dropzone->setFieldFile('tipoRequerimientoId');
                $dropzone->setRuta(DOC_ROOT."/requerimientos/");
                $dropzone->setFielPath('path');
            break;
            case 'archivo':
                $dropzone->setTable('archivo');
                $dropzone->setKeyTable('archivoId');
                $dropzone->setFieldFile('tipoArchivoId');
                $dropzone->setRuta(DOC_ROOT."/archivos/");
                $dropzone->setFielPath('path');
                $dropzone->setDateExpiration($_POST['datef']);
                break;
            case 'documento':
                $dropzone->setTable('documento');
                $dropzone->setKeyTable('documentoId');
                $dropzone->setFieldFile('tipoDocumentoId');
                $dropzone->setRuta(DOC_ROOT."/documentos/");
                $dropzone->setFielPath('path');
            break;
        }
        if(!$dropzone->doProcessFile()){
            echo "fail[#]";
            $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
        }else{
            echo "ok[#]";
            $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
            echo "[#]";
            //se retorna la actualizacion segun sea el tipo de archivo subido.
            switch ($_POST['tipoFile']){
                case 'requerimiento':
                    $requerimiento->setContractId($_POST['id']);
                    $requerimientos = $requerimiento->Enumerate();
                    $smarty->assign("requerimientos", $requerimientos);
                    $smarty->display(DOC_ROOT."/templates/lists/requerimiento.tpl");
                    echo "[#]contentRequerimientos";//nombre de elemento donde se lista los archivos para actualizar
                break;
                case 'archivo':
                    $archivo->setContractId($_POST['id']);
                    $archivos = $archivo->Enumerate();
                    $smarty->assign("archivos", $archivos);
                    $smarty->display(DOC_ROOT."/templates/lists/archivo.tpl");
                    echo "[#]contentArchivos";//nombre de elemento donde se lista los archivos para actualizar
                break;
                case 'documento':
                    $documento->setContractId($_POST['id']);
                    $documentos = $documento->Enumerate();
                    $smarty->assign("documentos", $documentos);
                    $smarty->display(DOC_ROOT."/templates/lists/documento.tpl");
                    echo "[#]contentDocumentos";//nombre de elemento donde se lista los archivos para actualizar
                    break;
            }

        }
    break;



}