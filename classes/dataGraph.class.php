<?php

class DataGraph extends contract{
    private  $data;
    public function getData() {
        return $this->data;
    }
    function chartAltasBajas () {
        global $monthsInt;
        $baseBajas = [];
        $baseAltas = [];
        for ($bb = 1; $bb <= 12; $bb++) {
            $cad['month'] = $monthsInt[$bb];
            $cad['total'] = 0;

            $baseAltas[$bb] = $cad;
            $baseBajas[$bb] = $cad;
        }
        $sql = "select date_format(fechaBaja, '%m') as mes, count(contractId) as total from contract
        where activo = 'No' and fechaBaja is not null and date_format(fechaBaja, '%Y') = date_format(CURRENT_DATE(), '%Y')
        group by date_format(fechaBaja, '%m')
        ";
        $this->Util()->DB()->setQuery($sql);
        $bajas =  $this->Util()->DB()->GetResult();

        foreach($bajas as $var) {
            $keyB = (int)$var['mes'];
            $baseBajas[$keyB]['total'] = $var['total'];
        }

        $sql = "select date_format(fechaAlta, '%m') as mes, count(contractId) as total from contract
        where fechaAlta is not null and date_format(fechaAlta, '%Y') = date_format(CURRENT_DATE(), '%Y')
        group by date_format(fechaAlta, '%m')
        ";
        $this->Util()->DB()->setQuery($sql);
        $altas =  $this->Util()->DB()->GetResult();
        foreach($altas as $var) {
            $keyA = (int)$var['mes'];
            $baseAltas[$keyA]['total'] = $var['total'];
        }
        $data['type'] = 'BarGroup';
        $data['title'] = 'Altas y bajas por mes';
        $data['xTitle'] = 'Meses';
        $data['yTitle'] = 'Total';
        $data['data1y'] = array_column($baseBajas, 'total');
        $data['data2y'] = array_column($baseAltas, 'total');
        $data['xAxis'] = array_column($baseAltas, 'month');
        $fileName = 'altas_bajas.png';
        $data['graphName'] = $fileName;
        $graph = new HuerinGraph($data);
        $graph->generateGraph();
        $cad['url'] = $fileName;
        $cad['name']= "graph";
        if(!is_array($this->data))
            $this->data = [];
        array_push($this->data, $cad);
    }

    public function chartTypePerson() {
        $sql = "select count(*) from contract where type = 'Persona Moral' and activo ='Si' ";
        $this->Util()->DB()->setQuery($sql);
        $morales = $this->Util()->DB()->GetSingle();

        $sql = "select count(*) from contract where type = 'Persona Fisica' and activo ='Si' ";
        $this->Util()->DB()->setQuery($sql);
        $fisica = $this->Util()->DB()->GetSingle();

        $data2 = array($morales, $fisica);

        $data['type'] = 'Bar';
        $data['title'] = 'Tipos de persona';
        $data['xTitle'] = 'Tipo persona';
        $data['yTitle'] = 'Total';
        $data['data1y'] = $data2;
        $data['xAxis'] = ['Persona moral', 'Persona fisica'];
        $fileName = 'chart_type_person.png';
        $data['graphName'] = $fileName;
        $graph = new HuerinGraph($data);
        $graph->generateGraph();
        $cad['url'] = $fileName;
        $cad['name']= "graph";
        if(!is_array($this->data))
            $this->data = [];
        array_push($this->data, $cad);
    }

    public function chartMonth13 () {
        $sql = "select count(*) from customer where active ='1' and noFactura13 = 'Si' ";
        $this->Util()->DB()->setQuery($sql);
        $noFacturan = $this->Util()->DB()->GetSingle();

        $sql = "select count(*) from customer where active ='1' and noFactura13 = 'No' ";
        $this->Util()->DB()->setQuery($sql);
        $siFacturan = $this->Util()->DB()->GetSingle();

        $data2 =array($noFacturan, $siFacturan);

        $data['type'] = 'Bar';
        $data['title'] = 'Empresas que generan factura mes 13';
        $data['xTitle'] = 'Generan factura 13';
        $data['yTitle'] = 'Total';
        $data['data1y'] = $data2;
        $data['xAxis'] = ['Si', 'No'];
        $fileName = 'chart_month_13.png';
        $data['graphName'] = $fileName;
        $graph = new HuerinGraph($data);
        $graph->generateGraph();
        $cad['url'] = $fileName;
        $cad['name']= "graph";
        if(!is_array($this->data))
            $this->data = [];
        array_push($this->data, $cad);
    }

    public function chartContracts () {
        global $personal, $customer;
        $responsables = $personal->GetIdResponsablesSubordinados();
        $totalActivos = count($customer->SuggestCustomerFilter([ 'tipos'=> 'activos', 'encargados'=> $responsables ]));

        $totalTemporal = count($customer->SuggestCustomerFilter([ 'tipos'=> 'temporal', 'encargados'=> $responsables ]));

        $totalInactivos = count($customer->SuggestCustomerFilter([ 'tipos'=> 'inactivos', 'encargados'=> $responsables ]));

        $data2 = array($totalActivos, $totalTemporal, $totalInactivos);

        $data['type'] = 'Bar';
        $data['title'] = 'Empresas activas, inactivas y baja temporal';
        $data['xTitle'] = 'Status';
        $data['yTitle'] = 'Total';
        $data['data1y'] = $data2;
        $data['xAxis'] = ['Activas', 'Inactivas', 'Baja temporal'];
        $fileName = 'chart_contract_status.png';
        $data['graphName'] = $fileName;
        $graph = new HuerinGraph($data);
        $graph->generateGraph();
        $cad['url'] = $fileName;
        $cad['name']= "graph";
        if(!is_array($this->data))
            $this->data = [];
        array_push($this->data, $cad);
    }
}
