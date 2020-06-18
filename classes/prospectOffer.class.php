<?php

class prospectOffer extends Prospect
{
    protected $data;
    public function generateArrayOffer() {
        $file  = DOC_ROOT . "/properties/config_offer_prospect.json";
        $string = file_get_contents($file);
        $prices = json_decode($string, true);
        $prospecto  = $this->info();
        $data =  [];

        foreach($prices['income_per_month'] as $var) {
            if(($var['min'] <= $prospecto['sale_amount_per_month']) && ($prospecto['sale_amount_per_month'] <= $var['max'])) {
                $cad['concept'] =  "Ingresos";
                $cad['price'] = $var['price'];
                array_push($data, $cad);
                break;
            }
        }

        foreach($prices['bank_account'] as $var){
            if(($var['min'] <= $prospecto['number_account_bank']) && ($prospecto['number_account_bank'] <= $var['max'])){
                $cad['concept'] =  "Bancos";
                $cad['price'] = $var['price'];
                array_push($data, $cad);
                break;
            }
        }

        foreach($prices['invoice_per_month'] as $var){
            if(($var['min'] <= $prospecto['invoice_per_month']) && ($prospecto['invoice_per_month'] <= $var['max'])){
                $cad['concept'] =  "Facturas";
                $cad['price'] = $var['price'];
                array_push($data, $cad);
                break;
            }
        }

        foreach($prices['deposit'] as $var){
            if(($var['min'] <= $prospecto['deposit_per_month']) && ($prospecto['deposit_per_month'] <= $var['max'])){
                $cad['concept'] =  "Depositos";
                $cad['price'] = $var['price'];
                array_push($data, $cad);
                break;
            }
        }

        foreach($prices['payment'] as $var){
            if(($var['min'] <= $prospecto['transfer_per_month']) && ($prospecto['transfer_per_month'] <= $var['max'])){
                $cad['concept'] =  "Pagos";
                $cad['price'] = $var['price'];
                array_push($data, $cad);
                break;
            }
        }

        foreach($prices['general_expense'] as $var){
            if(($var['min'] <= $prospecto['expense_per_month']) && ($prospecto['expense_per_month'] <= $var['max'])){
                $cad['concept'] =  "Gastos generales";
                $cad['price'] = $var['price'];
                array_push($data, $cad);
                break;
            }
        }
        foreach($prices['num_employee'] as $var){
            if(($var['min'] <= $prospecto['number_employee']) && ($prospecto['number_employee'] <= $var['max'])){
                $cad['concept'] =  "Numero empleados";
                $cad['price'] = $var['price'];
                array_push($data, $cad);
                break;
            }
        }
        return $data;
    }
    function generateData() {
        $info = $this->info();
        if($info['sale_amount_per_month'] > 0) {
              $sql  = "select a.nombreServicio, b.* 
                       from tipoServicio a 
                       inner join activity_service b on a.tipoServicioId =  b.service_id where a.tipoServicioId = '2' ";
              $this->Util()->DB()->setQuery($sql);
              $row =  $this->Util()->DB()->GetRow();
              if($row)
                  array_push($this->data, $row);
        }
        if($info['have_payroll'] > 0) {
            $sql  = "select a.nombreServicio, b.* 
                       from tipoServicio a 
                       inner join activity_service b on a.tipoServicioId =  b.service_id where a.tipoServicioId = '7' ";
            $this->Util()->DB()->setQuery($sql);
            $row =  $this->Util()->DB()->GetRow();
            if($row)
                array_push($this->data, $row);
        }
    }
}