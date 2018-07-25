<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 15/06/2018
 * Time: 02:29 PM
 */
use Dompdf\Dompdf;
class CreatePdfNotification
{
    public function __construct()
    {
       //$this->domPdf = new Dompdf();
        $this->smarty = new Smarty;
        $this->smarty->caching = false;
        $this->smarty->compile_check = true;

    }
    function CreateFileNotificationToContract($data=array()){
        global $monthsComplete;
        $file = "pendientes_".$data['contractId'].".pdf";
        $dompdf = new Dompdf();
        $this->smarty->assign('meses',$monthsComplete);
        $this->smarty->assign('data',$data);
        $html = $this->smarty->fetch(DOC_ROOT.'/templates/molds/moldWorkflowPendingByContract.tpl');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $content = $dompdf->output();
        file_put_contents(DOC_ROOT.'/sendFiles/'.$file,$content);
        //si se creo retornar true
        if(file_exists(DOC_ROOT."/sendFiles/".$file))
            return true;
        else
            return false;
    }
    function CreateFileNotificationToEncargados($data=array(),$personalId){
        global $monthsComplete;
        $file = "pendientes_".$personalId.".pdf";
        $dompdf = new Dompdf();
        $this->smarty->assign('meses',$monthsComplete);
        $this->smarty->assign('data',$data);
        $html = $this->smarty->fetch(DOC_ROOT.'/templates/molds/moldWorkflowPendingByEncargado.tpl');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $content = $dompdf->output();
        file_put_contents(DOC_ROOT.'/sendFiles/'.$file,$content);
        //si se creo retornar true
        if(file_exists(DOC_ROOT."/sendFiles/".$file))
            return true;
        else
            return false;
    }
}