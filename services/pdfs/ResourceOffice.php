<?php
use Dompdf\Dompdf;

class ResourceOffice extends Inventory
{
    private $domPdf;
    private $smarty;

    public function __construct()
    {
        //$this->domPdf = new Dompdf();
        $this->smarty = new Smarty;
        $this->smarty->caching = false;
        $this->smarty->compile_check = true;

    }
   public function generate($fileName="pdf_office_resource",$type = 'download'){

       ob_clean();
       $this->smarty->assign("info",$this->infoResource());
       $this->smarty->assign("upkeeps",$this->enumerateUpKeeps());
       $this->smarty->assign('DOC_ROOT', DOC_ROOT);
       $html = $this->smarty->fetch(DOC_ROOT.'/templates/molds/pdf-office-resource.tpl');
       $dompdf = new Dompdf();
       $dompdf->loadHtml($html);

       $dompdf->setPaper('A4', 'portrait');

       $dompdf->render();

       if($type == 'download') {
           $dompdf->stream($fileName.'.pdf');
       } else if($type == 'view') {
           $dompdf->stream($fileName.'.pdf', array("Attachment" => false));
       } else {
           return $dompdf->output();
       }

       exit(0);


   }
}