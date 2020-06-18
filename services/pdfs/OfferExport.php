<?php
use Dompdf\Dompdf;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\SimpleType\LineSpacingRule;
use PhpOffice\PhpWord\SimpleType\TextAlignment;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\Style\ListItem;
use PhpOffice\PhpWord\Style\Image;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Paper;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Writer\Word2007\Element;

//OfferExport
class OfferExport extends prospectOffer {

    private $outPath;
    private $templatePath;
    private $typeOffice;
    private $useTemplate;
    protected $object;

    /*
     * @var
     * default settings pages and textbox
     */
    protected $defaultConfigPages;
    protected $settingsDefaultTextBox;

   /*
    * @var
    * default font and paragraph styles
    */
    protected $pDefaultSettingsFontStyle;
    protected $title1FontStyle;
    protected $title2FontStyle;
    protected $defaultSettingsParagraph;
    protected $paragraphTitle1Style;
    protected $paragraphTitle2Style;

    /*
     * @var
     * default table styles
     * */
    protected $htDefaultSettingsFontStyle;
    protected $defaultTableStyle;
    protected $cellWhitBackground;

    public function __construct($type = 'Word') {
        $this->outPath = DOC_ROOT . "/sendFiles";
        $this->templatePath = DOC_ROOT . "/designs/templates";
        $this->useTemplate = false;
        $this->typeOffice =  $type;
        $this->object =  null;
        $this->data = [];

        $paper = new Paper('Letter');
        $this->defaultConfigPages = array(
            'marginTop' => Converter::cmToTwip(1.27),
            'marginLeft' => Converter::cmToTwip(1.27),
            'marginBottom' => Converter::cmToTwip(1.27),
            'marginRight' => Converter::cmToTwip(1.27),
            'pageSizeW' => $paper->getWidth(),
            'pageSizeH' => $paper->getHeight(),
        );

        $this->settingsDefaultTextBox = array(
            'width' => Converter::cmToPoint(18.16),
            'height' => Converter::cmToPoint(23.24),
            'borderColor' => 'none',
            'wrappingStyle' => 'infront',
            'positioning' => Image::POSITION_ABSOLUTE,
            'posVertical' => Image::POSITION_ABSOLUTE,
            'posHorizontal' => Image::POSITION_ABSOLUTE,
            'posVerticalRel' => Image::POSITION_RELATIVE_TO_TEXT,
            'posHorizontalRel' => Image::POSITION_RELATIVE_TO_COLUMN,
            'marginTop' => Converter::cmToPoint(0.11),
            'marginLeft' => Converter::cmToPoint(0.93),
        );

        $this->pDefaultSettingsFontStyle = array(
            'size' => 12,
            'name' => 'Arial',
            'color' => '#595959'
        );
        $this->htDefaultSettingsFontStyle = array(
            'size' => 14,
            'name' => 'Arial',
            'color' => '#FFFFFF'
        );
        $this->title1FontStyle = array(
            'size' => 16,
            'name' => 'Arial',
            'color' => '#595959',
            'bold' => true,
            'allCaps' => true,
            'underline' => 'single'
        );
        $this->title2FontStyle = array(
            'size' => 13,
            'name' => 'Arial',
            'color' => '#595959',
            'allCaps' => true,
            'underline' => 'single'
        );
        $this->defaultSettingsParagraph = array(
            'align' => Jc::BOTH,
            'spacing' => Converter::pointToTwip(4),
            'spacingLineRule' => LineSpacingRule::AUTO,
        );
        $this->paragraphTitle1Style = array(
            'align' => Jc::BOTH,
            'spacingLineRule' => LineSpacingRule::AUTO,
            'spaceAfter' => Converter::pointToTwip(10),
        );
        $this->paragraphTitle2Style = array(
            'align' => Jc::BOTH,
            'spacingLineRule' => LineSpacingRule::AUTO,
            'spaceBefore' => Converter::pointToTwip(10),
            'spaceAfter' => Converter::pointToTwip(10),
        );

        $this->defaultTableStyle = array(
                'borderSize' => Converter::pointToTwip(.5),
                'borderColor' => 'BFBFBF',
                'width' =>  100 * 50,
                'unit' => TblWidth::PERCENT,
                'position' => 'horzAnchor'
        );
        $this->cellWhitBackground = array(
          'bgColor' => '767171',
        );
    }
    public function setUseTemplate($value) {
        $this->useTemplate =  $value;
    }
    function portada() {

        $section = $this->object->addSection($this->defaultConfigPages);
        $styles =  array(
            'width' => Converter::cmToPoint(19.05),
            'wrappingStyle' => 'behind',
            'alignment' => Jc::CENTER,
            'positioning' => 'absolute',
            'posHorizontal'    => Image::POSITION_HORIZONTAL_CENTER,
        );
        $section->addImage(DOC_ROOT."/images/word/portada.jpg", $styles);
        $textbox =  $section->addTextBox(
            array(
                'width' => 400,
                'height' => 150,
                'borderColor' => 'none',
                'wrappingStyle' => 'infront',
                'positioning' => Image::POSITION_ABSOLUTE,
                'posVertical' => Image::POSITION_ABSOLUTE,
                'posHorizontal' => Image::POSITION_ABSOLUTE,
                'posVerticalRel' => Image::POSITION_RELATIVE_TO_MARGIN,
                'posHorizontalRel' => Image::POSITION_RELATIVE_TO_MARGIN,
                'marginTop' => Converter::cmToPoint(16.5),
                'marginLeft' => Converter::cmToPoint(5),
            )
        );
        $titleFontStyles = array(
            'size' => 18,
            'name' => 'Arial',
            'allCaps' => true,
             );
        $titleParStyles = array(
            'align' => 'center',
            'spacing' => Converter::pointToTwip(4),
            'spacingLineRule' => LineSpacingRule::AUTO,
        );
        $textbox->addText('Propuestas de servicios', $titleFontStyles, $titleParStyles);
        foreach ($this->data as $serv) {
            $textbox->addText($serv['nombreServicio'], $titleFontStyles, $titleParStyles);
        }
    }
    function genericSection($drawTextbox = false) {
        $page = new stdClass();
        $page->section = $this->object->addSection($this->defaultConfigPages);
        $styles =  array(
            'width' => Converter::cmToPoint(4.97),
            'height' => Converter::cmToPoint(24.75),
            'wrappingStyle' => 'behind',
            'positioning' => Image::POSITION_ABSOLUTE,
            'posHorizontal'    => Image::POSITION_HORIZONTAL_LEFT,
        );
        $page->section->addImage(DOC_ROOT."/images/word/side.jpg", $styles);
        if(!$drawTextbox)
            return $page;
        $page->textbox =  $page->section->addTextBox($this->settingsDefaultTextBox);
        return $page;
    }
    function presentationPage() {
        $page = $this->genericSection(true);
        $page->textbox->addText('CDMX, a '.$this->Util()->setFormatDate(date('Y-m-d')), '+Cuerpo',
            array('align' => Jc::END, 'spaceAfter' => Converter::cmToTwip(1.2), 'spacingLineRule' => LineSpacingRule::AUTO));
        $dear = $page->textbox->addTextRun(array('align' => Jc::START, 'spaceAfter' => Converter::cmToTwip(.85), 'spacingLineRule' => LineSpacingRule::AUTO));
        $dear->addText('Estimado Sr. ', array('bold' => true, 'name'=> 'Arial', 'size'=>12));
        $dear->addText($this->info()['name'], '+Cuerp');
        $page->textbox->addText('Por medio del presente convenio les describimos los alcances de los servicios solicitados por ustedes. ',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('De manera general todos los servicios que brindamos siguen los procesos más estrictos de calidad, además de cumplir cabalmente con las normas de información financiera y las diferentes leyes fiscales según correspondan. ',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Nuestro equipo de profesionales está basado en las reglas de ética y de servicio al cliente, que tenemos dentro de la firma, buscando siempre la satisfacción total de quienes depositan su confianza en nosotros.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('De ante mano les agradecemos esta oportunidad de servirles profesionalmente y les aseguramos que este trabajo recibirá nuestro mejor cuidado y atención.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Favor de confirmar su aceptación de los términos de esta carta convenio, firmando copia de la misma y devolviéndola a uno de nuestros representantes.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Estamos a sus órdenes para aclarar cualquier duda que pudiera existir en torno a esta propuesta.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Atentamente,',
            '+Cuerpo', array('spaceAfter'=> Converter::cmToTwip(3), 'spacingLineRule' => LineSpacingRule::AUTO));

        $page->textbox->addText('Jacobo Eduardo Huerin Romano', '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Contador Público Certificado', '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText(htmlspecialchars('Socio Fundador & CEO'), '+Cuerpo', $this->defaultSettingsParagraph);
    }
    function workplanPage() {
        $page = $this->genericSection(true);
        $page->textbox->addText('Nuestro plan de trabajo', 'title1', $this->paragraphTitle1Style);
        $page->textbox->addText('Ejecutaremos nuestro trabajo realizando actividades específicas, las cuales se resumen de la siguiente manera: ', '+Cuerpo', $this->defaultSettingsParagraph);

        $table =  $page->textbox->addTable($this->defaultTableStyle);
        $table->addRow();
        $table->addCell(10, $this->cellWhitBackground)->addText('Etapa','headerTable');
        $table->addCell(90, $this->cellWhitBackground)->addText('Actividades generales', 'headerTable');
        $expectation = [];
        foreach ($this->data as $var) {
            $table->addRow();
            $table->addCell(10)->addText(htmlspecialchars($var['nombreServicio']), '+Cuerpo');
            $cell = $table->addCell(90);
            $shortDescription = $var['short_description'] ? explode(PHP_EOL, $var['short_description']) : [];
            foreach($shortDescription as $shortDes) {
                $cell->addListItem(htmlspecialchars($shortDes), 0, '+Cuerpo', $this->defaultSettingsParagraph);
            }
            $currentExpectation = $var['expectation'] ? explode(PHP_EOL, $var['expectation']) : null;
            if(is_array($currentExpectation))
               $expectation = array_merge($expectation, $currentExpectation);
        }
        $page->textbox->addText('Expectativas del servicio', 'title2', $this->paragraphTitle2Style);
        $page->textbox->addText('En nuestra entrevista, nos percatamos de sus expectativas de servicio con respecto a nuestra participación como asesores externos, considerando que ustedes requieren, entre otros aspectos, lo siguiente:', '+Cuerpo', $this->defaultSettingsParagraph);
        foreach ($expectation as $item) {
            $page->textbox->addListItem(htmlspecialchars($item), 0, '+Cuerpo', $this->defaultSettingsParagraph);
        }
    }
    function personalPage() {
        $page = $this->genericSection(true);
        $page->textbox->addText('Profesionales participantes', 'title1', $this->paragraphTitle1Style);
        $page->textbox->addText('El equipo que seleccionaremos para la ejecución de los servicios de la sociedad incluye profesionales con amplia experiencia con el fin de brindarles el mejor servicio.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('El socio encargado del área de contabilidad e impuestos, será responsable de coordinar los servicios que se prestaran a su sociedad.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Es política de nuestra firma tener un gerente contable, conocido por ustedes y que además de estar familiarizado con sus operaciones, está para sustituir al socio encargado en su ausencia, o para trabajar conjuntamente con él cuando sea necesario.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Al mismo tiempo asignaremos a un supervisor de contabilidad, para realizar los servicios de manera directa, su responsabilidad será atenderlos personalmente además de resolver cualquier consulta o aclaración del servicio respectivamente.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('El supervisor junto con su equipo de trabajo, desempeñan las actividades basados en los criterios, procesos y procedimientos establecidos por la firma.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Es responsabilidad de los socios cerciorarse de que la sociedad reciba un buen servicio con un alto nivel de profesionalismo, por parte de cualquier miembro de la firma.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('De ser necesario, cuando lo consideremos conveniente, y dependiendo de los servicios especializados que puedan surgir, recurriremos a otros profesionales dentro de nuestra firma, expertos en dicho servicio.',
            '+Cuerpo', $this->defaultSettingsParagraph);
    }
    function serviceDescriptionPage() {
        if($this->object === null || empty($this->data))
            return false;

        $page = $this->genericSection(true);
        $page->textbox->addText('Descripcion de nuestros servicios', 'title1', $this->paragraphTitle1Style);
        $page->textbox->addText('Los servicios aquí mencionados se realizan en las instalaciones de la firma, aunque para la correcta comunicación, nuestro personal puede acudir a las instalaciones del cliente de forma periódica según se considere necesario.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Independientemente se realizarán las juntas necesarias para establecer y dar seguimiento a las políticas tanto de contabilidad e impuestos como de nóminas y seguridad social correspondientes.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('A su vez el registro de la contabilidad como el cálculo de la nómina se realizará de manera específica para su empresa basados en las políticas previamente establecidas con la administración de la misma.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Los papeles de trabajo preparados son propiedad de nuestra firma, contienen información confidencial y serán retenidos por nosotros de acuerdo con nuestras políticas y procedimientos.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Los servicios de contabilidad e impuestos como los de nómina y seguridad social se realizará basados en los criterios de la firma platicados previamente con la administración de la empresa los cuales cumplen completamente con las leyes fiscales correspondientes vigentes.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('De igual manera, la información proporcionada por ustedes para el desarrollo de nuestro trabajo estará debidamente protegida, ya que tenemos establecidas políticas relacionadas con la confidencialidad de la información generada.',
            '+Cuerpo', $this->defaultSettingsParagraph);

    }
    function listServicesPage() {
        if($this->object === null || empty($this->data))
            return false;

        foreach($this->data as $var) {
            $page = $this->genericSection(true);
            $page->textbox->addText('Listado de nuestros servicios', 'title1', $this->paragraphTitle1Style);
            $page->textbox->addText('Nuestro trabajo consistirá en realizar las siguientes actividades:',
                '+Cuerpo', $this->defaultSettingsParagraph);
            $page->textbox->addText(htmlspecialchars($var['nombreServicio']), 'title2', $this->paragraphTitle2Style);
            $shortDescription = $var['large_description'] ? explode(PHP_EOL, $var['large_description']) : [];
            foreach($shortDescription as $act) {
                $page->textbox->addListItem(htmlspecialchars($act), 0, '+Cuerpo', $this->defaultSettingsParagraph);
            }
        }

    }
    function requestInformationPage() {
        if($this->object === null || empty($this->data))
            return false;

        $page = $this->genericSection(true);
        $page->textbox->addText('informacion necesaria', 'title1', $this->paragraphTitle1Style);
        $page->textbox->addText('Con el propósito de que nuestro trabajo se realice sin limitaciones, la administración de la Sociedad, nos proporcionará entre otras cosas y con base a nuestra solicitud lo siguiente:',
            '+Cuerpo', $this->defaultSettingsParagraph);

        foreach($this->data as $var) {
            $page->textbox->addText(htmlspecialchars($var['nombreServicio']), 'title2', $this->paragraphTitle2Style);

            if($var['service_id'] == 2)
                $page->textbox->addText('Toda la documentación soporte y complementaria a más tardar en los primeros cinco días del mes inmediato posterior al mes a contabilizar.',
                '+Cuerpo', $this->defaultSettingsParagraph);

            $shortDescription = $var['request_information'] ? explode(PHP_EOL, $var['request_information']) : [];
            foreach($shortDescription as $info) {
                $page->textbox->addListItem(htmlspecialchars($info), 0, '+Cuerpo', $this->defaultSettingsParagraph);
            }
        }
        $page->textbox->addText('Asimismo, se designará por parte de ustedes, al personal que atenderá nuestras solicitudes de información.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('La entrega fuera de tiempo de la documentación ocasionará retraso en los cálculos de nómina y en la elaboración de los impuestos correspondientes, sin repercusión alguna para la firma.',
            '+Cuerpo', $this->defaultSettingsParagraph);
    }
    function workSchedulePage() {
        if($this->object === null || empty($this->data))
            return false;

        $page = $this->genericSection(true);
        $page->textbox->addText('programacion del trabajo', 'title1', $this->paragraphTitle1Style);
        $page->textbox->addText('A continuación, se indican las fechas programadas para: el inicio y conclusión de nuestro trabajo para la entrega de los informes y para otros eventos importantes, conforme a nuestros acuerdos previos.',
            '+Cuerpo', $this->defaultSettingsParagraph);

        foreach($this->data as $var) {
            $page->textbox->addText(htmlspecialchars($var['nombreServicio']), 'title2', $this->paragraphTitle2Style);
            $paragraphStyle = $this->defaultSettingsParagraph;
            $var['service_id'] == 2 ? $paragraphStyle['align'] = JC::START : '';
            $text = $page->textbox->addTextRun($paragraphStyle);
            $work_schedule = $var['work_schedule'] ? explode(PHP_EOL,$var['work_schedule']) : "";
            foreach($work_schedule as $schedule) {
                $text->addText(htmlspecialchars($schedule),'+Cuerpo');
                $text->addTextBreak();
            }
        }
    }
    function reportPage() {
        if($this->object === null || empty($this->data))
            return false;

        $page = $this->genericSection(true);
        $page->textbox->addText('informes a presentar', 'title1', $this->paragraphTitle1Style);
        $page->textbox->addText('Como resultado de nuestro trabajo, emitiremos los siguientes documentos:',
            '+Cuerpo', $this->defaultSettingsParagraph);

        foreach($this->data as $var) {
            $page->textbox->addText(htmlspecialchars($var['nombreServicio']), 'title2', $this->paragraphTitle2Style);
            $reports = $var['reports'] ? explode(PHP_EOL,$var['reports']) : "";
            foreach($reports as $report) {
                $page->textbox->addListItem(htmlspecialchars($report), 0, '+Cuerpo', $this->defaultSettingsParagraph);
            }
        }
        $page->textbox->addText('Nuestra responsabilidad de preparar y presentar la información a que está obligada la Sociedad, y la forma de hacerlo, dependerá de los hechos y circunstancias a la fecha de la presentación. ',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Si nuestra actuación tuviera alguna limitación, la razón de ello se explicará a los funcionarios correspondientes con toda oportunidad.',
            '+Cuerpo', $this->defaultSettingsParagraph);
    }
    function honorario() {
        $page = $this->genericSection(true);
        $page->textbox->addText('honorarios profesionales', 'title1', $this->paragraphTitle1Style);
        $page->textbox->addText('Nuestros honorarios profesionales, por los servicios anteriormente mencionados, están calculados en atención al nivel de experiencia y al tiempo por invertir de nuestro personal, ascenderán a la cantidad de $2,000.00 (dos mil pesos 00/100 MN). ',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Al monto de nuestros honorarios profesionales se deberá adicionar el correspondiente Impuesto al Valor Agregado.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('En el mes de diciembre de cada año se deberá cubrir doble (13ava iguala) que representa una compensación por los cálculos y declaraciones anuales.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('En caso de que se presente cualquier circunstancia que pueda modificar el monto de los honorarios profesionales propuestos, se los informaremos de inmediato.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('La iguala arriba mencionada está basada en la información proporcionada por ustedes al día de hoy, por lo que cada año en el mes de enero se hace una revaluación de la misma conforme a la información actualizada a esa fecha.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Los honorarios deberán de ser cubiertos dentro de los primeros cinco días del mes en que sea emitido el recibo. ',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Los honorarios por revisiones especiales, aclaraciones posteriores y/o por información que tengamos que proporcionar a las autoridades derivado de éstas revisiones, no están considerados, debido a la imposibilidad práctica de prever el tiempo que se requerirá para ello. Por lo tanto, dichos honorarios serán facturados conforme al tiempo incurrido, a la naturaleza y/o características de la revisión y de acuerdo a las cuotas vigentes de la firma.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Durante el desempeño de nuestras actividades y cuando amerite el pago de gastos adicionales, tales como gastos de viaje, transportación, etc., únicamente serán reportados y facturados por separado, con su previo acuerdo.',
            '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addText('Para poder cumplir adecuadamente con lo anteriormente enumerado, requerimos de su parte:',
            '+Cuerpo', $this->defaultSettingsParagraph);

        $page->textbox->addListItem('La entrega puntual y completa de la documentación', 0, '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addListItem('El pago puntual de los impuestos y demás contribuciones a su cargo.', 0, '+Cuerpo', $this->defaultSettingsParagraph);
        $page->textbox->addListItem('El pago puntual de los honorarios pactados.', 0, '+Cuerpo', $this->defaultSettingsParagraph);

    }
    function aceptacion() {
        $page = $this->genericSection(true);
        $page->textbox->addText('aceptacion del servicio', 'title1', $this->paragraphTitle1Style);
        $page->textbox->addText('Acepto las condiciones de esta carta convenio como un acuerdo de voluntades entre la Sociedad que represento y la firma de contadores públicos que llevara a cabo el servicio de contabilidad y nóminas antes citada. He leído y entiendo plenamente las condiciones y disposiciones contenidas. También confirmo que estoy facultado para suscribir este acuerdo de voluntades en nombre de “LA SOCIEDAD”.',
            '+Cuerpo', $this->defaultSettingsParagraph);

        $text = $page->textbox->addTextRun(array('align' => Jc::START,
            'spacingLineRule' => LineSpacingRule::AUTO,
            'spaceBefore' => Converter::pointToTwip(10),
            'spaceAfter' => Converter::pointToTwip(10),));
        $text->addText('ACEPTADO POR: ', 'title2' );
        $text->addTextBreak(2);
        $text->addText('PUESTO:', 'title2' );
        $text->addTextBreak(2);
        $text->addText('FIRMA: ', 'title2' );
        $text->addTextBreak(2);
        $text->addText('FECHA: ', 'title2' );
        $text->addTextBreak(2);

    }
    public function generate() {
        $this->object = $this->useTemplate ? new TemplateProcessor($this->templatePath."/template.docx") : new PhpWord();
        $this->object->getSettings()->setThemeFontLang(new Language(Language::ES_ES));
        $this->object->addFontStyle('+Cuerpo', $this->pDefaultSettingsFontStyle);
        $this->object->addFontStyle('title1', $this->title1FontStyle);
        $this->object->addFontStyle('title2', $this->title2FontStyle);
        $this->object->addFontStyle('headerTable', $this->htDefaultSettingsFontStyle);
        $this->generateData();
        $this->portada();
        $this->presentationPage();
        $this->workplanPage();
        $this->personalPage();
        $this->listServicesPage();
        $this->serviceDescriptionPage();
        $this->requestInformationPage();
        $this->workSchedulePage();
        $this->reportPage();
        $this->honorario();
        $this->aceptacion();


        //$this->object->addSection()->addTable()->addRow()
        /*
        $this->object->setValue('customerName', $info['legal_representative']);
        $this->object->setValue('sociedadName',$info['business_activity']);
        $this->object->setValue('expeditionPlace','CDMX');
        $this->object->setValue('largeDate',$this->Util()->setFormatDate(date('Y-m-d')));

        $tableStyles = array(
            'borderSize' => 12,
            'borderColor' => '#00000',
            'width' =>  10000,
            'unit' => TblWidth::TWIP,
            'position' => 'leftFromText'
        );
        $accountTaxTable = new Table($tableStyles);
        $data = $this->generateData();

        $accountTaxTable->addRow();
        $accountTaxTable->addCell(150)->addText('Etapa');
        $accountTaxTable->addCell(150)->addText('Actividades generales');
        $accountTaxTable->addRow();
        $accountTaxTable->addCell(150)->addText('Contabilidad e impuestos');
        $col = $accountTaxTable->addCell(150);
        $col->addListItem('Texto 1');
        $col->addListItem('Texto 2');
        $col->addListItem('Texto 3');
        $col->addListItem('Texto 4');

        $this->object->setComplexBlock('accountTax',$accountTaxTable);*/

        header("Content-Disposition: attachment; filename=propuesta.docx");
        //$this->object->saveAs("php://output");

        /*header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document." );// you should look for the real header that you need if it's not Word 2007!!!
        header( 'Content-Disposition: attachment; filename=propuesta.docx' );

        $pathpdf = $this->outPath."/";
        //exec("soffice --convert-to pdf:writer_pdf_Export --outdir $pathpdf $path");*/
        $objWriter = IOFactory::createWriter( $this->object, "Word2007" );
        $objWriter->save( "php://output" );
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