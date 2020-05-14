<?php
use Dompdf\Dompdf;

class OfferPdf extends prospectOffer
{
    private $domPdf;
    private $smarty;
    public function __construct() {
        $this->smarty = new Smarty;
        $this->smarty->caching = false;
        $this->smarty->compile_check = true;
   }

   public function generate($fileName="offer_prospect", $type = 'download') {
       ob_clean();
       $this->smarty->assign("offer",$this->generateArrayOffer());
       $this->smarty->assign("prospect",$this->info());
       $this->smarty->assign('DOC_ROOT', DOC_ROOT);
       $html = $this->smarty->fetch(DOC_ROOT.'/templates/molds/basic-prospect.tpl');
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