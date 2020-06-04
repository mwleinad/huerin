<?php
use Dompdf\Dompdf;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;

//OfferExport
class OfferExport extends prospectOffer {

    private $outPath;
    private $templatePath;
    private $typeOffice;
    private $useTemplate;
    protected $object;

    public function __construct($type = 'Word') {
        $this->outPath = DOC_ROOT . "/sendFiles";
        $this->templatePath = DOC_ROOT . "/designs/templates";
        $this->useTemplate = false;
        $this->typeOffice =  $type;
        $this->object =  null;
    }
    public function setUseTemplate($value) {
        $this->useTemplate =  $value;
    }
    public function generate() {
        $this->object = $this->useTemplate ? new TemplateProcessor($this->templatePath."/template.docx") : new PhpWord();
        $info = $this->info();
        $this->object->setValue('customerName', $info['legal_representative']);
        $this->object->setValue('sociedadName',$info['business_activity']);
        $this->object->setValue('expeditionPlace','CDMX');
        $this->object->setValue('largeDate',$this->Util()->setFormatDate(date('Y-m-d')));

        $tableStyles = array(
            'borderSize' => 12,
            'borderColor' => '#00000',
            'width' =>  6000,
            'unit' => TblWidth::TWIP,
            'position' => 'leftFromText'
        );
        $accountTaxTable = new Table($tableStyles);
        $accountTaxTable->addRow();
        $accountTaxTable->addCell(150)->addText('Etapa');
        $accountTaxTable->addCell(150)->addText('Actividades generales');
        $this->object->setComplexBlock('accountTax',$accountTaxTable);

        header("Content-Disposition: attachment; filename=propuesta.docx");
        $this->object->saveAs("php://output");

        /*header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document." );// you should look for the real header that you need if it's not Word 2007!!!
        header( 'Content-Disposition: attachment; filename=propuesta.docx' );

        $pathpdf = $this->outPath."/";
        //exec("soffice --convert-to pdf:writer_pdf_Export --outdir $pathpdf $path");
        $objWriter = IOFactory::createWriter( $this->object, "Word2007" );
        $objWriter->save( "php://output" );*/
        exit;
    }
    /*public function generate($fileName="offer_prospect", $type = 'download') {
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
    }*/
}