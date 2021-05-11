<?php

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class AcuseResource extends Inventory
{
    public function generateResponsiva () {
        global $monthsComplete;
        $data =  $this->infoResource();
        //encontrar los precios

        $precioEquipo = $data['costo_recuperacion'];
        $precioDispositivos = 0;

        foreach($data['device_resource'] as $devresource)
            $precioDispositivos += $devresource['costo_recuperacion'];

        $precioTotal = $precioEquipo + $precioDispositivos;
        $numLetra = new CNumeroaLetra ();
        $numLetra->setMayusculas(1);
        $numLetra->setGenero(1);
        $numLetra->setMoneda('PESOS');
        $numLetra->setDinero(1);
        $numLetra->setPrefijo('');
        $numLetra->setSufijo('');
        $numLetra->setNumero($precioTotal);
        $precioLetra = $numLetra->letra();
        $word = new TemplateProcessor(DOC_ROOT . "/designs/templates/responsiva.docx");

        $word->setValue('tipo_equipo', $data['tipo_equipo']);
        $word->setValue('marca_equipo', $data['marca']);
        $word->setValue('modelo_equipo', $data['modelo']);
        $word->setValue('no_serie', $data['no_serie']);
        $word->setValue('observacion', $data['descripcion']);

        $typeDispositivos = ['ventilador', 'cable_ventilador', 'hubusb', 'monitor', 'mouse', 'mousepad', 'ethernet', 'teclado',
            'nobreak', 'hdmi','convertidor_hdmi', 'convertidor_vga'];
        $devices_inline = array_column($data['device_resource'], 'tipo_dispositivo');
        foreach ($typeDispositivos as $dev) {
           $find = array_search($dev, $devices_inline);
           $word->setValue('marca_'. $dev,  $find !== false ? $data['device_resource'][$find]['marca'] : '--');
           $word->setValue('modelo_'. $dev, $find !== false ? $data['device_resource'][$find]['modelo'] :  '--');
           $word->setValue('noserie_'. $dev, $find !== false ? $data['device_resource'][$find]['no_serie'] :  '--');
           $word->setValue('observation_'. $dev, $find !== false ? $data['device_resource'][$find]['descripcion'] :  '--');
        }

        $word->setValue('monto_recuperacion', "$".number_format($precioTotal, 2,'.', ',') );
        $word->setValue('monto_recuperacion_letra', $precioLetra);
        $word->setValue('usuario',$data['responsables'][0]['nombre'] );
        $dateExplode = explode('-', date('Y-m-d'));
        $fecha = $dateExplode[2]." de ".$monthsComplete[$dateExplode[1]]. " del ". $dateExplode[0];
        $word->setValue('fecha', $fecha);
        $name = "responsiva_".date('Y-m-d H:i:s');
        $name = str_replace("-", "_", $name);
        $name = str_replace(":", "_", $name);
        $name = $name.".docx";
        $word->saveAs( DOC_ROOT."/sendFiles/".$name);
        if(is_file(DOC_ROOT."/sendFiles/".$name))
            $this->setNameReport($name);
    }
}
