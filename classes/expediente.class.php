<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 15/03/2018
 * Time: 11:12 AM
 */

class Expediente extends Main
{
  public function Enumerate(){
      $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery('SELECT * FROM expedientes WHERE status="activo" ORDER BY name ASC');
      $result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
      return $result;
  }

}