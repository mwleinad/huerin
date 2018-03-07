<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 05/03/2018
 * Time: 11:38 PM
 */

class Serie extends Main
{
   private $serieId;
   public function setSerieId($value){
       $this->serieId = $value;
   }
   public function EnumerateOnePage(){
      $sql = 'SELECT * FROM serie WHERE 1 GROUP BY serie ORDER BY  serie ASC';
      $this->Util()->DB()->setQuery($sql);
      $result =  $this->Util()->DB()->GetResult();

      return $result;
   }
}