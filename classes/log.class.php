<?php

class Log extends Util
{
	private $personalId;
	private $fecha;
	private $tabla;
	private $tablaId;
	private $action;
	private $oldValue;
	private $newValue;
	
	public function setPersonalId($value){
		$this->Util()->ValidateInteger($value);
		$this->personalId = $value;
	}
	
	public function setFecha($value){
		$this->fecha = $value;		
	}
	
	public function setTabla($value){
		$this->tabla = $value;		
	}
	
	public function setTablaId($value){
		$this->tablaId = $value;		
	}
	
	public function setAction($value){
		$this->action = $value;		
	}
	
	public function setOldValue($value){
		$this->oldValue = $value;		
	}
	
	public function setNewValue($value){
		$this->newValue = $value;		
	}
	public function Save(){
				
		$sql = "INSERT INTO log(personalId, fecha, tabla, tablaId, action, oldValue, newValue)
				 VALUES ('".$this->personalId."', '".$this->fecha."', '".$this->tabla."', '".$this->tablaId."',
				 '".$this->action."', '".$this->oldValue."', '".$this->newValue."')";								
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->InsertData();


		$body ="<pre>";
		//quien realizo el cambio
        $this->Util()->DB()->setQuery('SELECT name FROM personal WHERE personalId="'.$this->personalId.'" ');
        $who = $this->Util()->DB()->GetSingle();
        $encargados=  array();
        $jefes = array();
        //componer mensaje de accion
        switch($this->action){
            case 'Insert':
                $accion = "ha sido dado de alta ";
            break;
            case 'Update':
                $accion ="ha sido modificada ";
            break;
            case 'Baja':
                $accion="ha sido  dado de baja ";
            break;
            case 'Reactivacion':
                $accion="ha sido reactivado ";
            break;
            case 'Delete':
                $accion="ha sido eliminado ";
             break;
        }
        //encontrar tabla que se modifico
        switch($this->tabla){//se comprueba de que tabla se hace la modificacion.
            case 'contract':
                $sql  ="SELECT a.permisos,a.name as razon,b.nameContact as cliente FROM contract a INNER JOIN customer b ON a.customerId=b.customerId WHERE a.contractId='".$this->tablaId."' ";
                $this->Util()->DB()->setQuery($sql);
                $contrato = $this->Util()->DB()->GetRow();
                $permisos = explode('-',$contrato['permisos']);

                foreach($permisos as $perm){
                    list($dep,$per) = explode(',',$perm);
                    if($per>0)
                    {
                        $this->Util()->DB()->setQuery('SELECT * FROM personal WHERE personalId="'.$per.'" ');
                        $row = $this->Util()->DB()->GetRow();
                        if($this->Util()->ValidateEmail($row['email'])){
                            $encargados[$row['email']] = $row['name'];
                            //encontramos los jefes de forma ascendente de los encargados de cuenta
                            $personal= new Personal();
                            $yourJefes= $personal->Jefes($row['personalId']);
                            $jefes = array_merge($jefes,$yourJefes);
                        }

                    }
                }
                $body .="La sigiuiente razon social : ".$contrato['razon']." del cliente ".$contrato['cliente']."<br>";
                $body .=$accion."  por el colaborador ".$who."<br><br>";
                break;
            case 'servicio':
                $sql  ="SELECT c.nombreServicio,b.name as razon,b.permisos FROM servicio a 
                                  INNER JOIN contract b ON a.contractId=b.contractId 
                                  INNER JOIN tipoServicio c ON a.tipoServicioId=c.tipoServicioId WHERE a.servicioId='".$this->tablaId."' ";
                $this->Util()->DB()->setQuery($sql);
                $registro = $this->Util()->DB()->GetRow();
                $permisos = explode('-',$registro['permisos']);
                foreach($permisos as $perm){
                    list($dep,$per) = explode(',',$perm);
                    if($per>0)
                    {
                        $this->Util()->DB()->setQuery('SELECT * FROM personal WHERE personalId="'.$per.'" ');
                        $row = $this->Util()->DB()->GetRow();
                        if($this->Util()->ValidateEmail($row['email'])){
                            $encargados[$row['email']] = $row['name'];
                            //encontramos los jefes de forma ascendente de los encargados de cuenta
                            $personal= new Personal();
                            $yourJefes= $personal->Jefes($row['personalId']);
                            $jefes = array_merge($jefes,$yourJefes);
                        }

                    }

                }
                if($this->action=="Insert"){
                    $body .="El servicio ".$registro['nombreServicio']." ".$accion."  para la razon ".$registro['razon']."<br>";
                    $body .="por el colaborador ".$who."<br><br>";
                }
                else{
                    $body .="El servicio ".$registro['nombreServicio']." de la razon social(contrato) ".$registro['razon']."<br>";
                    $body .=$accion." por el colaborador ".$who."<br><br>";
                    }
                break;
            case 'customer'://en la edicion de cliente este deberia llegarle en teoria solo a jacobo y rogelio que son los que revisan operaciones.
                $sql  ="SELECT nameContact FROM customer  WHERE customerId='".$this->tablaId."' ";
                $this->Util()->DB()->setQuery($sql);
                $cliente= $this->Util()->DB()->GetSingle();
                $body .="El cliente : ".$cliente."<br>";
                $body .=$accion." por el colaborador ".$who."<br><br>";
                break;

        }
		switch($this->action){
            case 'Update'://si es update se necesitaria comparar que cambio se realizo
                  $changes = $this->FindOnlyChanges($this->oldValue,$this->newValue);
                  if(empty($changes['after']))
                      return false;
                  $body .="<br><br>En la parte de abajo encontrata la lista de cambios realizados: <br>";
                  $body .="<table>
                        <thead>
                          <tr>
                            <th colspan='2' style='text-align: center'>Datos anteriores</th>
                            <th colspan='2' style='text-align: center'>Datos nuevos</th>
                          </tr>
                          <tr>
                            <th style='text-align: left'>Campo</th>
                            <th style='text-align: left;border-right:1px solid'>Valor</th>
                            <th style='text-align: left'>Campo</th>
                            <th style='text-align: left'>Valor</th>
                          </tr>
                        </thead><tbody>";
                  foreach($changes['after'] as $ck=>$vc){
                       $body .="<tr>
                                    <td style='padding:0px 8px 4px 0px;text-align: left'>".$changes['before'][$ck]['campo'].": </td>
                                    <td style='padding:0px 8px 4px 0px;text-align: left;border-right:1px solid'>".$changes['before'][$ck]['valor']."</td>
                                    <td style='padding:0px 8px 4px 0px;text-align: left'>".$vc['campo'].": </td>
                                    <td style='padding:0px 8px 4px 0px;text-align: left'>".$vc['valor']."</td>
                                </tr>";
                  }
                    $body .="</tbody>";
            break;
        }
        //encontrar correos de los jefes de cada encargado, esto siempre se debe cumplor debido  a que todos tiene jefe inmediato hasta llegar a jacobo
        $correosJefes=array();
        if(!empty($jefes))
        {
            $jefes = array_unique($jefes);
            $ids = implode(',',$jefes);
            $this->Util()->DB()->setQuery('SELECT email,name FROM personal WHERE personalId IN('.$ids.') AND active="1" ');
            $resultJefes = $this->Util()->DB()->GetResult();
            foreach($resultJefes as $var){
                if($this->Util()->ValidateEmail($var['email'])){
                    $correosJefes[$var['email']] =$var['name'];
                }
            }
        }
        $encargados = array_merge($encargados,$correosJefes);
        $mail = new SendMail();
        $subject = 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
        $mail->PrepareMultipleHidden($subject,$body,$encargados,'',"","","","",FROM_MAIL);
		return true;				
	}
	
  	function GetLog(){
	
    	$this->Util()->DB()->setQuery(
        "SELECT
          comprobante.comprobanteId,comprobante.userId,comprobante.fecha, personal.name
        FROM
          comprobante
        LEFT JOIN 
          instanciaServicio ON instanciaServicio.comprobanteId = comprobante.comprobanteId
        LEFT JOIN
          personal ON personal.personalId=comprobante.userId
        ORDER BY
          comprobante.fecha DESC
        LIMIT 
          0 , 1000"
    	);
    	$idsUsers = $this->Util()->DB()->GetResult();
		
    	return $idsUsers;
  	}
  	function SearchLog($values){
  	    $filter ="";
  	    if($values['modulo']!="")
  	        $filter .=" AND log.tabla='".$values['modulo']."' ";
        if($values['finicial']!="")
            $filter .=" AND log.fecha>='".$values['finicial']." 00:00:00' ";
        if($values['ffinal']!="")
            $filter .=" AND log.fecha<='".$values['ffinal']." 23:59:59' ";

         $sql = "SELECT date(log.fecha) as fecham,log.*,personal.name as usuario FROM log LEFT JOIN personal ON log.personalId=personal.personalId where 1 ".$filter."   ORDER BY log.fecha ASC";
         $this->Util()->DB()->setQuery($sql);
         $result = $this->Util()->DB()->GetResult();

         return $result;
    }
    function FindOnlyChanges($before,$after){
	     $beforeUnserialize = unserialize($before);
	     $afterUnserialize = unserialize($after);
	     $news=array();
         $olds=array();
	     $llavesExcluidas =array('lastUpdate','inicioFacturaMysql','inicioOperacionesMysql','lastModified','modifiedBy','lastUpdated');
	     foreach($beforeUnserialize as $key =>$value){
             if(in_array($key,$llavesExcluidas))
                 continue;

             $cad = array();
             $cad2=array();
             if(trim($value)!=trim($afterUnserialize[$key]))
             {
                 $field = $this->FindNameField($key);
                 switch($key){
                     case 'sociedadId':
                         $this->Util()->DB()->setQuery("SELECT nombreSociedad FROM sociedad WHERE sociedadId='".$beforeUnserialize[$key]."' ");
                         $valorBefore = $this->Util()->DB()->GetSingle();
                         $this->Util()->DB()->setQuery("SELECT nombreSociedad FROM sociedad WHERE sociedadId='".$afterUnserialize[$key]."' ");
                         $valorAfter = $this->Util()->DB()->GetSingle();
                     break;
                     case 'responsableCuenta':
                         $this->Util()->DB()->setQuery("SELECT name FROM personal WHERE personalId='".$beforeUnserialize[$key]."' ");
                         $valorBefore = $this->Util()->DB()->GetSingle();
                         $this->Util()->DB()->setQuery("SELECT name FROM personal WHERE personalId='".$afterUnserialize[$key]."' ");
                         $valorAfter = $this->Util()->DB()->GetSingle();
                         break;
                     default:
                     case 'tipoServicioId':
                         $this->Util()->DB()->setQuery("SELECT nombreServicio FROM tiposervicio WHERE tipoServicioId='".$beforeUnserialize[$key]."' ");
                         $valorBefore = $this->Util()->DB()->GetSingle();
                         $this->Util()->DB()->setQuery("SELECT nombreServicio FROM tiposervicio WHERE tipoServicioId='".$afterUnserialize[$key]."' ");
                         $valorAfter = $this->Util()->DB()->GetSingle();
                         break;
                     case 'permisos':
                         $valorBefore="";
                         $valorAfter="";
                         //desglozar los permisos antetiores
                         $permisosBefore = explode("-",$beforeUnserialize[$key]);
                         $depsBefore = array();
                         foreach($permisosBefore as $pb){
                             list($depb,$perb) = explode(',',$pb);
                             $depsBefore[$depb] = $perb;
                         }
                         $permisosAfter = explode("-",$afterUnserialize[$key]);
                         $depsAfter = array();
                         foreach($permisosAfter as $pa){
                             list($depa,$pera) = explode(',',$pa);
                             $depsAfter[$depa] = $pera;
                         }
                         foreach($depsBefore as $kb=>$vb){
                             if($depsBefore[$kb]==$depsAfter[$kb])
                                 unset($depsAfter[$kb]);
                         }

                         if(!empty($depsAfter)){
                            foreach($depsAfter as $ka=>$va){
                                $this->Util()->DB()->setQuery(" SELECT departamento FROM departamentos WHERE departamentoId='".$ka."' ");
                                $nameDep = $this->Util()->DB()->GetSingle() ;
                                $this->Util()->DB()->setQuery("SELECT name FROM personal WHERE personalId='".$depsBefore[$ka]."' ");
                                $persBefore = $this->Util()->DB()->GetSingle() ;
                                $this->Util()->DB()->setQuery("SELECT name FROM personal WHERE personalId='".$depsAfter[$ka]."' ");
                                $persAfter = $this->Util()->DB()->GetSingle() ;

                                $valorBefore .="Encargado de ".$nameDep." : ".$persBefore."<br>";
                                $valorAfter .="Encargado de ".$nameDep." : ".$persAfter."<br>";
                            }
                         }
                     break;
                     default:
                         $valorBefore =$beforeUnserialize[$key];
                         $valorAfter = $afterUnserialize[$key];
                     break;

                 }
                 $cad2['valor'] = $valorBefore;
                 $cad2['campo'] = $field;
                 $olds[] = $cad2;
                 $cad['valor'] =$valorAfter;
                 $cad['campo'] = $field;
                 $news[] =  $cad;
             }
	     }

      $data['before']=$olds;
	  $data['after'] = $news;
	  return $data;
    }
    function FindNameField($key){
	    $this->Util()->DB()->setQuery('SELECT name FROM nameFields WHERE clave="'.$key.'" ');
	    $field =$this->Util()->DB()->GetSingle();
	    if(!$field)
	        $field ="Campo indefinido";

        return $field;
    }

  		
}//Log
