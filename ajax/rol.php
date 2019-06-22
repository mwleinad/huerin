<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST['type']){
    case 'open_config':
        $id = $_POST['id'];
        $rol->setRolId($id);
        $role = $rol->Info();
        $modulos = $rol->GetConfigRol();
        $roles = $rol->Enumerate();
        //dd($modulos);exit;
        $smarty->assign('roles',$roles);
        $smarty->assign('info',$role);
        $smarty->assign('modulos',$modulos);
        $smarty->display(DOC_ROOT.'/templates/boxes/config-rol-popup.tpl');
    break;
    case 'save_config':
        $rol->setRolId($_POST['id']);
        if(!$rol->SaveConfigRol())
        {
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        else
        {
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }

        break;
    case 'copyPermiso':
        $id = $_POST['id'];
        $baseId = $_POST['baseId'];
        $rol->setRolId($id);
        $role = $rol->Info();
        $rol->setRolId($baseId);
        $modulos = $rol->GetConfigRol();

        $roles = $rol->Enumerate();
        //dd($modulos);exit;
        $smarty->assign('roles',$roles);
        $smarty->assign('info',$role);
        $smarty->assign('modulos',$modulos);
        $smarty->display(DOC_ROOT.'/templates/forms/config-rol.tpl');
        break;
    case 'addRol':
        $deps =  $departamentos->GetListDepartamentos();
        $smarty->assign('deps',$deps);
        $smarty->assign('title','Agregar Rol');
        $smarty->display(DOC_ROOT.'/templates/boxes/add-rol-popup.tpl');
    break;
    case 'addPorcentBono':
        $smarty->assign('deps',$deps);
        $smarty->assign('title','Agregar registro');
        $smarty->display(DOC_ROOT.'/templates/boxes/porcent-bono-popup.tpl');
        break;
    case 'saveRol':
         $rol->setDepartamentoId($_POST['depId']);
         $rol->setName($_POST['name']);
         if($rol->Save())
         {

             $roles = $rol->Enumerate();
             $smarty->assign('roles',$roles);
             echo "ok[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
             echo "[#]";
             $smarty->display(DOC_ROOT.'/templates/lists/rol.tpl');

         }
         else{
             echo "fail[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
         }
    break;
    case 'editRol':
        $deps =  $departamentos->GetListDepartamentos();
        $rol->setRolId($_POST['id']);
        $post = $rol->Info();
        $smarty->assign('deps',$deps);
        $smarty->assign('post',$post);
        $smarty->assign('title','Editar Rol');
        $smarty->display(DOC_ROOT.'/templates/boxes/add-rol-popup.tpl');
    break;
    case 'editPorcent':
        $rol->setPorcentId($_POST['id']);
        $post = $rol->InfoPorcent();
        $smarty->assign('post',$post);
        $smarty->assign('title','Editar porcentaje');
        $smarty->display(DOC_ROOT.'/templates/boxes/porcent-bono-popup.tpl');
        break;
    case 'updateRol':
        $rol->setRolId($_POST['rolId']);
        $rol->setDepartamentoId($_POST['depId']);
        $rol->setName($_POST['name']);
        if($rol->Update())
        {
            $roles = $rol->Enumerate();
            $smarty->assign('roles',$roles);
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/lists/rol.tpl');

        }
        else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;
    case 'deleteRol':
        $rol->setRolId($_POST['id']);
        if($rol->Delete())
        {
            $roles = $rol->Enumerate();
            $smarty->assign('roles',$roles);
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/lists/rol.tpl');

        }
        else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;
    case 'export':
        include(DOC_ROOT.'/libs/excel/PHPExcel.php');
        $styles = array(
            'styleLetMe' => array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => '47f08b')
                             )
                          ),
            'styleBold' => array(
                            'font' => array('bold'=>true,)
                        )
        );

        $roles = $rol->Enumerate();
        $book =  new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');

        foreach($roles as $key=>$value){
            //crear una pestaña por cada rol
            $sheet = $book->createSheet($key);
            $sheet->setCellValue('A1','Permisos del rol '.$value['name']);
            $sheet->setTitle($value['name']);

            //iterar primer nivel
            $rol->setRolId($value['rolId']);
            $yours = $rol->GetConfigRol();
            $row = 4;
            foreach($yours as $ky =>$vy){
                $col = $vy['levelDeep'];
                $cell = PHPExcel_Cell::stringFromColumnIndex($col);
                $cell2 = PHPExcel_Cell::stringFromColumnIndex($col+1);
                if($vy['letMe']){
                    $sheet->getStyle($cell.$row)->applyFromArray($styles['styleLetMe']);
                    $sheet->getStyle($cell2.$row)->applyFromArray($styles['styleLetMe']);
                }
                $sheet->getStyle($cell.$row)->applyFromArray($styles['styleBold']);
                if(!empty($vy['children'])){
                    $sheet->setCellValueByColumnAndRow($col,$row,$vy['titulo']);

                    $back['padre'] = 'no';
                    $back['init']  = 1;
                    $row++;
                    $rol->DrawChildrenExcel($row,$sheet,$vy['children'],$styles);
                }else{
                    $sheet->setCellValueByColumnAndRow($col,$row,$vy['titulo']);
                    $row++;
                }


            }
            $sheet->calculateColumnWidths();

        }
        $book->setActiveSheetIndex(0);
        switch($_POST['tipo']){
            case 'pdf':
                $rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
                //$rendererLibrary = 'tcPDF5.9';
                //$rendererLibrary = 'mPDF';
                $rendererLibrary = '';
                $rendererLibraryPath = DOC_ROOT.'/pdf/' . $rendererLibrary;
                PHPExcel_Settings::setPdfRenderer(
                    $rendererName,
                    $rendererLibraryPath);

                $book->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                $book->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

                $writer = new PHPExcel_Writer_PDF($book);
                $writer = PHPExcel_IOFactory::createWriter($book, 'PDF');
                $writer->save(DOC_ROOT."/sendFiles/roles.pdf");
            break;
            default:
                $writer= PHPExcel_IOFactory::createWriter($book, 'Excel2007');
                $writer->save(DOC_ROOT."/sendFiles/roles.xlsx");
            break;

        }
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/roles.".$_POST['tipo'];
    break;
    case 'savePorcentBono':
        $rol->setNamePorcent($_POST["name"]);
        $rol->setPorcentaje($_POST["porcentaje"]);
        $rol->setCategoria($_POST["categoria"]);
        if($rol->SavePorcent())
        {
            $porcentajes = $rol->EnumeratePorcentajes();
            $smarty->assign('porcentajes',$porcentajes);
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/lists/porcent-bonos.tpl');
        }
        else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }

    break;
    case 'updatePorcentBono':
        $rol->setPorcentId($_POST["porcentId"]);
        $rol->setPorcentaje($_POST["porcentaje"]);
        $rol->setCategoria($_POST["categoria"]);
        if($rol->UpdatePorcent())
        {
            $porcentajes = $rol->EnumeratePorcentajes();
            $smarty->assign('porcentajes',$porcentajes);
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/lists/porcent-bonos.tpl');

        }
        else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
    }
    break;
    case 'deletePorcent':
        $rol->setPorcentId($_POST['id']);
        if($rol->DeletePorcent())
        {
            $porcentajes = $rol->EnumeratePorcentajes();
            $smarty->assign('porcentajes',$porcentajes);
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/lists/porcent-bonos.tpl');

        }
        else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
    }
}