<?php

class prospectOffer extends Prospect
{
    protected $data;
    public function getPrice($key, $amount) {
        $file  = DOC_ROOT . "/properties/config_offer_prospect.json";
        $string = file_get_contents($file);
        $prices = json_decode($string, true);
        $price = 0;
        if(!array_key_exists($key, $prices))
            return $price;
        foreach($prices[$key] as $var) {
            if(($var['min'] <= $amount) && ($amount <= $var['max'])) {
                $price = $var['price'];
                break;
            }
        }
        return $price;
    }
    function generateData() {
        $info = $this->info();
        if($info['sale_amount_per_month'] > 0) {
              $sql  = "select a.nombreServicio, b.* 
                       from tipoServicio a 
                       inner join activity_service b on a.tipoServicioId =  b.service_id where a.tipoServicioId = '2' ";
              $this->Util()->DB()->setQuery($sql);
              $row =  $this->Util()->DB()->GetRow();
              if($row) {
                  $row['price'] = $this->getPrice('sale_amount_per_month', $info['sale_amount_per_month']);
                  array_push($this->data, $row);
              }

        }
        if($info['have_payroll'] > 0 || $info['number_employee'] > 0) {
            $sql  = "select a.nombreServicio, b.* 
                       from tipoServicio a 
                       inner join activity_service b on a.tipoServicioId =  b.service_id where a.tipoServicioId = '7' ";
            $this->Util()->DB()->setQuery($sql);
            $row =  $this->Util()->DB()->GetRow();
            if($row) {
                $row['price'] = $this->getPrice('number_employee', $info['number_employee']);
                array_push($this->data, $row);
            }

        }
    }
}