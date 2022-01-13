<?php
use Dompdf\Dompdf;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;

class Bitacora extends Main {

    public function enumerate()
    {
        $this->Util()->DB()->setQuery('SELECT COUNT(*) FROM bitacora_importacion WHERE isnull(fecha_eliminado)');
        $total = $this->Util()->DB()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/importacion");

        $sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
        $this->Util()->DB()->setQuery('SELECT * FROM bitacora_importacion WHERE ISNULL(fecha_eliminado) ORDER BY id ASC '.$sql_add);
        $result = $this->Util()->DB()->GetResult();

        $data["items"] = $result;
        $data["pages"] = $pages;
        return $data;
    }
    function getDataBitacora($id) {
        $sql_ret =  "select a.fecha_registro, b.contract_name,
                            CONCAT('[',
                            GROUP_CONCAT(
                             JSON_OBJECT('servicio_id', a.servicio_id,'service_name', b.service_name, 'antes', a.antes, 'despues', a.despues)
                            ),
                            ']') as servicios
                            FROM bitacora_cambio_servicio a 
                            INNER JOIN(
                            SELECT sa.servicioId, sb.nombreServicio as service_name, sc.contractId, sc.name AS contract_name
                            FROM servicio sa
                            INNER JOIN tipoServicio sb ON sa.tipoServicioId = sb.tipoServicioId
                            INNER JOIN contract sc ON sa.contractId = sc.contractId) AS b ON a.servicio_id = b.servicioId
                            WHERE a.id_bitacora_importacion = '".$id."'
                            GROUP BY b.contractId";
        $this->Util()->DB()->setQuery($sql_ret);
        $resultados = $this->Util()->DB()->GetResult();
        $normalize_resultados = [];
        foreach($resultados as $resultado) {
            $servicios =  json_decode($resultado['servicios'], true);
            $resultado['servicios'] = $servicios;
            foreach($resultado['servicios'] as $ks => $servi) {
                $resultado['servicios'][$ks]['antes'] = json_decode($servi['antes'], true);
                $resultado['servicios'][$ks]['despues'] = json_decode($servi['despues'], true);
            }
            array_push($normalize_resultados, $resultado);
        }

        return $normalize_resultados;
    }

    function bitacoraToArchivo($data = []) {
        $dompdf =  new Dompdf();
        $this->Util()->Smarty()->assign("data",$data);
        $html =  $this->Util()->Smarty()->fetch(DOC_ROOT."/templates/molds/pdf-update-bitacora-servicio-masivo.tpl");
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $fileName  = uniqid().".pdf";
        $output =  $dompdf->output();
        file_put_contents(DOC_ROOT."/sendFiles/$fileName", $output);
        return  DOC_ROOT."/sendFiles/$fileName";
    }


    function enviarBitacora ($id) {
        $data = $this->getDataBitacora($id);
        if (count($data)) {
            $file = $this->bitacoraToArchivo($data);
            $send =  new SendMail();
            $subject = PROJECT_STATUS === 'test' ? 'Bitacora de cambios en servicios test' : 'Bitacora de cambios en servicios';
            $send->Prepare($subject,"Se han realizado actualizaciones en los servicios de las empresas.",EMAILCOORDINADOR,"COORDINADOR",$file,'bitacora.pdf',"","","noreplye@braunhuerin.com.mx","Administrador plataforma");
            @unlink($file);
        }
    }

    function generarPdfRecotizacion($data = []) {
        global $monthsComplete;
        $tableStyles = array(
            'borderSize' => 1,
            'borderColor' => '#00000',
            'width' =>  '100',
            'unit' => TblWidth::AUTO,
            'alignment' => PhpOffice\PhpWord\SimpleType\JcTable::CENTER

        );
        $fecha = date('d'). " de ".$monthsComplete[date('m')]." ".date('Y');

        $phpWord = new TemplateProcessor(DOC_ROOT."/sendFiles/template_recotizacion.docx");
        $fecha_documento = new TextRun();
        $fecha_documento->addText($fecha,
            array('bold' => true,
                'color' => '767070',
                'name'=>'Tw Cen MT',
                'size'=>12 ));
        $phpWord->setComplexValue('fecha', $fecha_documento);

        $nombre_cliente = new TextRun();
        $nombre_cliente->addText($data['nameContact'],
            array('bold' => true,
                  'color' => '767070',
                  'name'=>'Tw Cen MT',
                  'size'=>16 ));
        $phpWord->setComplexValue('nombre_cliente', $nombre_cliente);

        $nombre_empresa = new TextRun();
        $nombre_empresa->addText($data['name'],
            array('bold' => true,
                'color' => '767070',
                'name'=>'Tw Cen MT',
                'size'=>16 ));
        $phpWord->setComplexValue('nombre_empresa', $nombre_empresa);

        $table = new Table($tableStyles);
        $headerTable = array('name' => 'Tw Cen MT', 'size' => 12, 'bold' => true,'color'=>'767070');
        $table->addRow();
        $table->addCell()->addText('Servicio', $headerTable);
        $table->addCell()->addText('Costo Anterior', $headerTable);
        $table->addCell()->addText('Costo Nuevo', $headerTable);

        $bodyTable = array('name' => 'Tw Cen MT', 'size' => 12,'color'=>'767070');
        foreach($data['servicios'] as $serv) {
            $table->addRow();
            $table->addCell()->addText($serv['nombreServicio'], $bodyTable);
            $table->addCell()->addText($serv['costoAnterior'], $bodyTable);
            $table->addCell()->addText($serv['costoActual'], $bodyTable);
        }
        $phpWord->setComplexBlock('tabla_comparativa', $table);

        $name_file = uniqid();
        $file = DOC_ROOT.'/sendFiles/'.$name_file.'.docx';
        $phpWord->saveAs($file);

        return is_file($file) ?  $file : false;
    }
    function enviarRecotizacion($id) {
        $sql = "CALL sp_get_data_recotizacion('".$id."')";
        $this->Util()->DB()->setQuery($sql);
        $items = $this->Util()->DB()->GetResult();
        foreach($items as $item) {
            $item['servicios'] = json_decode($item['servicios'], true);
            $file = $this->generarPdfRecotizacion($item);

            $name_file = $item['rfc'].".docx";
            if($file) {
                $send =  new SendMail();
                $subject = PROJECT_STATUS === 'test' ? 'Envio de recotizacion test' : 'Envio de recotizacion';
                $send->Prepare($subject,"Se hace llegar la recotización.",EMAILCOORDINADOR,"COORDINADOR",$file,$name_file,"","","noreplye@braunhuerin.com.mx","Administrador plataforma");
                unlink($file);
            }
           break;
        }
    }

}

?>