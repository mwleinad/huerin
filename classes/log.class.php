<?php
use Dompdf\Dompdf;
class Log extends Util
{
	private $personalId;
	private $fecha;
	private $tabla;
	private $tablaId;
	private $action;
	private $oldValue;
	private $newValue;
	private $serviciosAfectados = [];
	private $contractsAfectados = [];

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
    public function setServiciosAfectados($value){
        $this->serviciosAfectados = $value;
    }
    public function setContractsAfectados($value){
        $this->contractsAfectados = $value;
    }
    public function GetCurrentData($table, $field, $id){
	    $sql  = "select * from $table where $field = '$id' ";
	    $this->Util()->DB()->setQuery($sql);
	    return $this->Util()->DB()->GetRow();
    }
	public function SaveOnly(){
	    global $personal;
	    $newValue =  strlen($this->newValue) > 0 ? htmlspecialchars($this->newValue, ENT_QUOTES) : '';
        $oldValue =  strlen($this->oldValue) > 0 ? htmlspecialchars($this->oldValue, ENT_QUOTES) : '';
	    $currentUser = $personal->getCurrentUser();
        $sql = "INSERT INTO log(personalId, fecha, tabla, tablaId, action, oldValue, newValue,namePerson)
				 VALUES ('".$currentUser["personalId"]."', '".$this->fecha."', '".$this->tabla."', '".$this->tablaId."',
				 '".$this->action."', '".$oldValue."', '".$newValue."','".$currentUser["name"]."')";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->InsertData();
        return true;
    }
	public function Save(){
	    global $personal;
        $currentUser = $personal->getCurrentUser();
		$sql = "INSERT INTO log(personalId, fecha, tabla, tablaId, action, oldValue, newValue,namePerson)
				 VALUES ('".$currentUser["personalId"]."', '".$this->fecha."', '".$this->tabla."', '".$this->tablaId."',
				 '".$this->action."', '".htmlspecialchars($this->oldValue, ENT_QUOTES)."', '".htmlspecialchars($this->newValue, ENT_QUOTES)."','".$currentUser["name"]."')";
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->InsertData();

		$body ="<pre>";
		//quien realizo el cambio
        $who = $currentUser["name"];

        $encargados=  array();
        $jefes = array();
        //componer mensaje de accion
        $wherehuerin="";
        $excluyehuerin=true;
        $sendBraun = SEND_LOG_BRAUN;
        $defaultId= array();
        switch($this->action){
            case 'Insert':
                $accion = "ha sido dado de alta ";
                //array_push($defaultId,IDHUERIN);
                //array_push($defaultId,319);
                $sendBraun = false;
            break;
            case 'Update':
                $excluyehuerin = true;
                $accion ="ha sido modificada ";
            break;
            case 'Baja':
                $accion="ha sido  dado de baja ";
                array_push($defaultId,IDHUERIN);
                array_push($defaultId,319);
                $sendBraun = false;
            break;
            case 'bajaParcial':
                $accion="ha sido  dado de baja temporalmente ";
                array_push($defaultId,IDHUERIN);
                array_push($defaultId,319);
                $sendBraun = false;
                break;
            case 'Reactivacion':
                $accion="ha sido reactivado ";
                array_push($defaultId,IDHUERIN);
                array_push($defaultId,319);
                $sendBraun = false;
            break;
            case 'readonly':
                $excluyehuerin = true;
                $accion="ha sido reactivado para solo lectura ";
            break;
            case 'Delete':
                $accion="ha sido eliminado ";
                array_push($defaultId,IDHUERIN);
                array_push($defaultId,319);
                $sendBraun = false;
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
                    //recursos humanos y mensajeria excluidos. y solo le debe llegar el supervisor para arriba.
                    if($dep==33||$dep==32)
                        continue;

                    if($per>0)
                    {
                        $this->Util()->DB()->setQuery("SELECT * FROM personal WHERE personalId='".$per."' ");
                        $row = $this->Util()->DB()->GetRow();
                        if($this->Util()->ValidateEmail(trim($row['email']))){
                            $encargados[trim($row['email'])] = $row['name'];
                            //encontramos los jefes de forma ascendente de los encargados de cuenta
                            $personal= new Personal();
                            $yourJefes= $personal->Jefes($row['personalId']);
                            $jefes = array_merge($jefes,$yourJefes);
                        }

                    }
                }
                if(!empty($jefes)&&$sendBraun){
                    array_push($jefes,IDBRAUN);
                }
                //si no tiene ningun encargado se envia a los gerentes(excluido mensajeria y RRHH) , coordinador y socio.
                if(empty($encargados))
                {
                    if($sendBraun)
                        array_push($defaultId,IDBRAUN);

                    $sqlo  ="SELECT email,name FROM personal  WHERE (LOWER(puesto) LIKE'%gerente%') OR personalId IN (".implode(',',$defaultId).")";
                    $this->Util()->DB()->setQuery($sqlo);
                    $persons= $this->Util()->DB()->GetResult();
                    foreach($persons as $pers)
                    {
                        if($pers['departamentoId']==33||$pers['departamentoId']==32)
                            continue;

                        if($this->Util()->ValidateEmail(trim($pers['email'])))
                            $encargados[trim($pers['email'])] = $pers['name'];
                    }
                }
                $body .="La siguiente razón social : ".$contrato['razon']." del cliente ".$contrato['cliente']."<br>";
                $body .=$accion."  por el colaborador ".$who."<br>";
                break;
            case 'servicio':
                if($sendBraun)
                    array_push($defaultId,IDBRAUN);

                $sql  ="SELECT c.nombreServicio,b.name as razon,b.permisos,c.departamentoId FROM servicio a 
                                  INNER JOIN contract b ON a.contractId=b.contractId 
                                  INNER JOIN tipoServicio c ON a.tipoServicioId=c.tipoServicioId WHERE a.servicioId='".$this->tablaId."' ";
                $this->Util()->DB()->setQuery($sql);
                $registro = $this->Util()->DB()->GetRow();
                $permisos = explode('-',$registro['permisos']);
                foreach($permisos as $perm){
                    list($dep,$per) = explode(',',$perm);
                    //recursos humanos y mensajeria excluidos. y solo le debe llegar el supervisor para arriba.
                    if($dep==33||$dep==32 || $dep != $registro['departamentoId'])
                        continue;

                    if($per>0)
                    {
                        $this->Util()->DB()->setQuery("SELECT * FROM personal WHERE personalId='".$per."' ");
                        $row = $this->Util()->DB()->GetRow();
                        if($this->Util()->ValidateEmail(trim($row['email']))){
                            $encargados[trim($row['email'])] = $row['name'];
                            //encontramos los jefes de forma ascendente de los encargados de cuenta
                            $personal= new Personal();
                            $yourJefes= $personal->Jefes($row['personalId']);
                            $jefes = array_merge($jefes,$yourJefes);
                        }
                    }
                }
                //si no tiene ningun encargado se envia a los gerentes(excluido mensajeria y RRHH) , coordinador y socio.
                if(empty($encargados))
                {
                    $sqlo  ="SELECT email,name FROM personal  WHERE (LOWER(puesto) LIKE'%gerente%') OR personalId IN (".implode(',',$defaultId).")";
                    $this->Util()->DB()->setQuery($sqlo);
                    $persons= $this->Util()->DB()->GetResult();
                    foreach($persons as $pers)
                    {
                        if($pers['departamentoId']==33||$pers['departamentoId']==32 || $pers['departamentoId'] != $registro['departamentoId'])
                        continue;

                        if($this->Util()->ValidateEmail($pers['email']))
                            $encargados[trim($pers['email'])] = trim($pers['name']);
                    }
                }
                if($this->action=="Insert"){
                    $body .="El servicio ".$registro['nombreServicio']." ".$accion."  para la razon ".$registro['razon']."<br>";
                    $body .="por el colaborador ".$who."<br>";
                }
                else{
                    $body .="El servicio ".$registro['nombreServicio']." de la razon social(contrato) ".$registro['razon']."<br>";
                    $body .=$accion." por el colaborador ".$who."<br>";
                    }
                break;
            case 'customer'://en la edicion de cliente este deberia llegarle en teoria solo a jacobo y rogelio que son los que revisan operaciones.
                //enviar a los gerentes(excluido mensajeria y RRHH) , coordinador y socio.
                if($sendBraun)
                    array_push($defaultId,IDBRAUN);

                $sqlp  ="SELECT email,name,departamentoId FROM personal  WHERE (LOWER(puesto) LIKE'%gerente%') OR personalId IN (".implode(',',$defaultId).")";
                $this->Util()->DB()->setQuery($sqlp);
                $persons= $this->Util()->DB()->GetResult();
                foreach($persons as $per)
                {
                    if($per['departamentoId']==33||$per['departamentoId']==32)
                        continue;

                    if($this->Util()->ValidateEmail($per['email']))
                        $encargados[trim($per['email'])] = $per['name'];
                }

                $sql  ="SELECT nameContact FROM customer  WHERE customerId='".$this->tablaId."' ";
                $this->Util()->DB()->setQuery($sql);
                $cliente= $this->Util()->DB()->GetSingle();
                $body .="El cliente : ".$cliente."<br>";
                $body .=$accion." por el colaborador ".$who."<br>";
            break;
        }
		switch($this->action){
            case 'Update'://si es update se necesitaria comparar que cambio se realizo
                  $changes = $this->FindOnlyChanges($this->oldValue,$this->newValue);
                  if(empty($changes['after']))
                      return false;

                $this->Util()->Smarty()->assign("body",$body);
                $this->Util()->Smarty()->assign("changes",$changes);
                $html =  $this->Util()->Smarty()->fetch(DOC_ROOT."/templates/molds/pdf-log-update-general.tpl");
                $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
                $dompdf =  new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $fileName  = $_SESSION['User']['userId']."_log.pdf";
                $output =  $dompdf->output();
                file_put_contents(DOC_ROOT."/sendFiles/$fileName", $output);
            break;
            case 'Reactivacion';
            case 'Baja':
            case 'bajaParcial':
            case 'readonly':
            case 'Insert':
                $changes = $this->FindFieldDetail($this->newValue);
                if(!empty($changes)) {
                    $this->Util()->Smarty()->assign("body",$body);
                    $this->Util()->Smarty()->assign("changes",$changes);
                    if(count($this->serviciosAfectados)>0)
                        $this->Util()->Smarty()->assign("serviciosAfectados",$this->serviciosAfectados);

                    if(count($this->contractsAfectados)>0)
                        $this->Util()->Smarty()->assign("contractsAfectados",$this->contractsAfectados);

                    $html =  $this->Util()->Smarty()->fetch(DOC_ROOT."/templates/molds/pdf-log-general.tpl");
                    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
                    $dompdf =  new Dompdf();
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    $fileName  = $_SESSION['User']['userId']."_log.pdf";
                    $output =  $dompdf->output();
                    file_put_contents(DOC_ROOT."/sendFiles/$fileName", $output);
                }
            break;
        }
        //encontrar correos de los jefes de cada encargado, esto siempre se debe cumplor debido  a que todos tiene jefe inmediato hasta llegar a jacobo
        $correosJefes=array();
        if(!empty($jefes))
        {
            $jefes = array_unique($jefes);
            //comprobar si se excluye a huerin
            if($excluyehuerin){
                $index = array_search(IDHUERIN,$jefes);
                if($index)
                    unset($jefes[$index]);
            }

            if(!$sendBraun){
                $index = array_search(IDBRAUN,$jefes);
                if($index)
                    unset($jefes[$index]);
            }

            if (count($jefes) > 0) {
                $ids = implode(',', $jefes);
                $this->Util()->DB()->setQuery('SELECT email,name FROM personal WHERE personalId IN(' . $ids . ') AND active="1" ');
                $resultJefes = $this->Util()->DB()->GetResult();
                foreach ($resultJefes as $var) {
                    if ($this->Util()->ValidateEmail(trim($var['email']))) {
                        $correosJefes[trim($var['email'])] = $var['name'];
                    }
                }
            }
        }
        $encargados = array_merge($encargados,$correosJefes);

        if(!SEND_LOG_MOD) {
            $encargados = [];
            $encargados['hbcruz@braunhuerin.com.mx'] = 'Dev Local';
        }

        $encargados =  $this->verificarIgnorados($encargados);

        if(file_exists( DOC_ROOT."/sendFiles/$fileName")){
            $file = DOC_ROOT."/sendFiles/$fileName";
        }
        else{
            $file="";
            $fileName="";
        }
        $mail = new SendMail();
        $subject = PROJECT_STATUS === 'test' ? 'NOTIFICACION DE CAMBIOS EN TEST' : 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
        $mail->PrepareMultipleNotice($subject,utf8_decode($body),$encargados,'',$file,$fileName,"","",'noreply@braunhuerin.com.mx','Administrador de plataforma');
        if(file_exists( $file)) {
           unlink($file);
        }
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
	     $beforeUnserialize = is_array(unserialize($before)) ? unserialize($before) : [];
	     $afterUnserialize = is_array(unserialize($after)) ? unserialize($after) : [];
	     $news=array();
         $olds=array();
	     $llavesExcluidas =array('cxcSaldoFavor','lastUpdate','inicioFacturaMysql','inicioOperacionesMysql',
                                 'lastModified','modifiedBy','lastUpdated','fechaMysql','customerId','contractId',
                                 'active','encargadoCuenta','responsableCuenta','customerId','cerFiel','keyFiel',
                                 'reqFiel','cerSellos','keySellos','reqSellos','idse1','idse2','idse3',
                                 'auxiliarCuenta','cobrador','nombreRegimen','nombreSociedad','nombreComercial',
                                 'tipoDePersona','lastDateCreateWorkflow','fechaBaja','lastProcessInvoice','fechaBaja',
                                 'fechaAlta');
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
                     case 'alternativeRzId':
                         $this->Util()->DB()->setQuery("SELECT name FROM contract WHERE contractId='".$beforeUnserialize[$key]."' ");
                         $valorBefore = $this->Util()->DB()->GetSingle();
                         $valorBefore = $beforeUnserialize[$key] === '0' ? 'Otros datos' : $valorBefore;
                         $valorBefore = $valorBefore == '0' ? '' : $valorBefore;

                         $this->Util()->DB()->setQuery("SELECT name FROM contract WHERE contractId='".$afterUnserialize[$key]."' ");
                         $valorAfter = $this->Util()->DB()->GetSingle();
                         $valorAfter = $afterUnserialize[$key] === '0' ? 'Otros datos' : $valorAfter;
                         $valorAfter = $valorAfter == '0' ? '' : $valorAfter;
                     break;
                     case 'regimenId':
                         $this->Util()->DB()->setQuery("SELECT nombreRegimen FROM regimen WHERE regimenId='".$beforeUnserialize[$key]."' ");
                         $valorBefore = $this->Util()->DB()->GetSingle();
                         $this->Util()->DB()->setQuery("SELECT nombreRegimen FROM regimen WHERE regimenId='".$afterUnserialize[$key]."' ");
                         $valorAfter = $this->Util()->DB()->GetSingle();
                     break;
                     case 'type_referred':
                         $valorBefore = $beforeUnserialize[$key] === 'partner' ? 'Asociado' : 'Otro';
                         $valorAfter  = $afterUnserialize[$key] === 'partner' ? 'Asociado' : 'Otro';
                     break;
                     case 'tipoServicioId':
                         $this->Util()->DB()->setQuery("SELECT nombreServicio FROM tipoServicio WHERE tipoServicioId='".$beforeUnserialize[$key]."' ");
                         $valorBefore = $this->Util()->DB()->GetSingle();
                         $this->Util()->DB()->setQuery("SELECT nombreServicio FROM tipoServicio WHERE tipoServicioId='".$afterUnserialize[$key]."' ");
                         $valorAfter = $this->Util()->DB()->GetSingle();
                         break;
                     case 'actividadComercialId':
                         $this->Util()->DB()->setQuery("SELECT name FROM actividad_comercial WHERE id='".$beforeUnserialize[$key]."' ");
                         $valorBefore = $this->Util()->DB()->GetSingle();
                         $this->Util()->DB()->setQuery("SELECT name FROM actividad_comercial WHERE id='".$afterUnserialize[$key]."' ");
                         $valorAfter = $this->Util()->DB()->GetSingle();
                         break;
                     case 'idTipoClasificacion':
                         $this->Util()->DB()->setQuery("SELECT nombre FROM tipo_clasificacion WHERE id='".$beforeUnserialize[$key]."' ");
                         $valorBefore = $this->Util()->DB()->GetSingle();
                         $this->Util()->DB()->setQuery("SELECT nombre FROM tipo_clasificacion WHERE id='".$afterUnserialize[$key]."' ");
                         $valorAfter = $this->Util()->DB()->GetSingle();
                         break;
                     case 'tipo_clasificacion_cliente_id':
                         $this->Util()->DB()->setQuery("SELECT nombre FROM tipo_clasificacion_cliente WHERE id='".$beforeUnserialize[$key]."' ");
                         $valorBefore = $this->Util()->DB()->GetSingle();
                         $this->Util()->DB()->setQuery("SELECT nombre FROM tipo_clasificacion_cliente WHERE id='".$afterUnserialize[$key]."' ");
                         $valorAfter = $this->Util()->DB()->GetSingle();
                         break;
                     case 'permisos':
                         $this->Util()->DB()->setQuery("SELECT departamentoId, departamento FROM departamentos where lower(departamento) not like '%mensajeria%' order by departamento ASC ");
                         $arrayDeps = $this->Util()->DB()->GetResult();
                         $valorBefore="";
                         $valorAfter="";
                         //desglozar los permisos anteriores
                         $permisosBefore = explode("-",$beforeUnserialize[$key]);
                         $depsBefore = array();
                         foreach($permisosBefore as $pb){
                             list($depb,$perb) = explode(',',$pb);
                             if($depb<=0 || $perb<=0)
                                 continue;

                             $depsBefore[$depb] = $perb;
                         }
                         $permisosAfter = explode("-",$afterUnserialize[$key]);
                         $depsAfter = array();
                         foreach($permisosAfter as $pa){
                             list($depa,$pera) = explode(',',$pa);
                             if($depa<=0 || $pera<=0)
                                 continue;

                             $depsAfter[$depa] = $pera;
                         }
                         if(!is_array($arrayDeps))
                             $arrayDeps = array();

                         foreach($arrayDeps as $kad=>$vad){
                             if(trim($depsBefore[$vad['departamentoId']]) == trim($depsAfter[$vad['departamentoId']]))
                                 continue;

                             $this->Util()->DB()->setQuery("SELECT name FROM personal WHERE personalId='".$depsBefore[$vad['departamentoId']]."' ");
                             $row = $this->Util()->DB()->GetRow() ;
                             $persBefore = $row['name'];

                             $this->Util()->DB()->setQuery("SELECT name FROM personal WHERE personalId='".$depsAfter[$vad['departamentoId']]."' ");
                             $row = $this->Util()->DB()->GetRow();
                             $persAfter =  $row['name'];

                             if($persBefore=="")
                                 $persBefore ="Sin encargado";
                             if($persAfter=="")
                                 $persAfter ="Sin encargado";

                             $valorBefore .="Encargado de ".$vad['departamento']." : ".($persBefore)."<br>";
                             $valorAfter  .="Encargado de ".$vad['departamento']." : ".($persAfter)."<br>";
                         }
                     break;
                     default:
                         $valorBefore =$beforeUnserialize[$key];
                         $valorAfter = $afterUnserialize[$key];
                     break;
                 }
                 if($valorAfter!='' || $valorBefore != ''){
                     $cad2['valor'] = $valorBefore;
                     $cad2['campo'] = $field;
                     $olds[] = $cad2;
                     $cad['valor'] =$valorAfter;
                     $cad['campo'] = $field;
                     $news[] =  $cad;
                 }
             }
	     }
      $data['before']=$olds;
	  $data['after'] = $news;
	  return $data;
    }
    function FindFieldDetail($elements){
        $allElements = unserialize($elements);
        $news=array();
        $llavesExcluidas =array('cxcSaldoFavor','lastUpdate','inicioFacturaMysql','inicioOperacionesMysql',
                                'lastModified','modifiedBy','lastUpdated','fechaMysql','customerId',
                                'contractId','active','encargadoCuenta','responsableCuenta','customerId',
                                'cerFiel','keyFiel','reqFiel','cerSellos','keySellos','reqSellos','idse1',
                                'idse2','idse3','auxiliarCuenta','cobrador','nombreRegimen','nombreSociedad',
                                'nombreComercial','tipoDePersona','lastDateCreateWorkflow','fechaBaja',
                                'lastProcessInvoice','fechaBaja','fechaAlta');
        foreach($allElements as $key =>$value){
            if(in_array($key,$llavesExcluidas))
                continue;

            $cad = array();
            $field = $this->FindNameField($key);
            switch($key){
                case 'sociedadId':
                    $this->Util()->DB()->setQuery("SELECT nombreSociedad FROM sociedad WHERE sociedadId='".$allElements[$key]."' ");
                    $valorBefore = $this->Util()->DB()->GetSingle();
                    break;
                case 'alternativeRzId':
                    $this->Util()->DB()->setQuery("SELECT name FROM contract WHERE contractId='".$allElements[$key]."' ");
                    $valorBefore = $this->Util()->DB()->GetSingle();
                    $valorBefore = isset($allElements[$key]) ? $valorBefore : '';
                    break;
                case 'regimenId':
                    $this->Util()->DB()->setQuery("SELECT nombreRegimen FROM regimen WHERE regimenId='".$allElements[$key]."' ");
                    $valorBefore = $this->Util()->DB()->GetSingle();
                break;
                case 'type_referred':
                    $valorBefore = $allElements[$key] === 'partner' ? 'Asociado' : 'Otro';
                    break;
                case 'actividadComercialId':
                    $this->Util()->DB()->setQuery("SELECT name FROM actividad_comercial WHERE id='".$allElements[$key]."' ");
                    $valorBefore = $this->Util()->DB()->GetSingle();
                break;
                case 'idTipoClasificacion':
                    $this->Util()->DB()->setQuery("SELECT nombre FROM tipo_clasificacion WHERE id='".$allElements[$key]."' ");
                    $valorBefore = $this->Util()->DB()->GetSingle();
                    break;
                case 'tipo_clasificacion_cliente_id':
                    $this->Util()->DB()->setQuery("SELECT nombre FROM tipo_clasificacion_cliente WHERE id='".$allElements[$key]."' ");
                    $valorBefore = $this->Util()->DB()->GetSingle();
                    break;
                case 'tipoServicioId':
                    $this->Util()->DB()->setQuery("SELECT nombreServicio FROM tipoServicio WHERE tipoServicioId='".$allElements[$key]."' ");
                    $valorBefore = $this->Util()->DB()->GetSingle();
                    break;
                case 'permisos':
                    $this->Util()->DB()->setQuery("SELECT departamentoId,departamento FROM departamentos where lower(departamento) not like '%mensajeria%' order by departamento ASC");
                    $arrayDeps = $this->Util()->DB()->GetResult();
                    $valorBefore="";
                    //encontrar los encargados
                    $permisosBefore = explode("-",$allElements[$key]);
                    $depsBefore = array();
                    foreach($permisosBefore as $pb){
                        list($depb,$perb) = explode(',',$pb);
                        if($depb<=0 || $perb<=0)
                            continue;

                        $depsBefore[$depb] = $perb;
                    }

                    foreach($arrayDeps as $ak=>$av){
                        $this->Util()->DB()->setQuery("SELECT name FROM personal WHERE personalId='".$depsBefore[$av['departamentoId']]."' ");
                        $persBefore = $this->Util()->DB()->GetSingle() ;
                        if($persBefore=="")
                            $persBefore ="Sin encargado";

                        $valorBefore .="Encargado de ".$av['departamento']." : ".$persBefore."<br>".chr(13).chr(10);
                    }
                    break;
                default:
                    $valorBefore =$allElements[$key];
                break;
            }
               if($valorBefore !== ""){
                   $cad['valor'] =$valorBefore;
                   $cad['campo'] = $field;
                   $news[] =  $cad;
               }

        }
        return $news;
    }
    function FindNameField($key){
	    $this->Util()->DB()->setQuery('SELECT name FROM nameFields WHERE clave="'.$key.'" ');
	    $field =$this->Util()->DB()->GetSingle();
	    if(!$field)
	        $field ="Campo indefinido";

        return utf8_decode($field);
    }
    function PrintInFormatText($changes,$tipo='complete'){
	    $html="";
	    switch($tipo){
            case 'complete':
                $html .="<table class='full-width'>
                        <thead>
                          <tr>
                            <th colspan='4' style='text-align: center;font-size: 10px;font-weight: bold'>Cambios realizados</th>
                          </tr>
                          <tr>
                            <th colspan='2' style='text-align: center;font-size: 10px;font-weight: bold'>Informacion anterior</th>
                            <th colspan='2' style='text-align: center;font-size: 10px;font-weight: bold'>Informacion nueva</th>
                          </tr>
                          <tr>
                            <th style='text-align: left;border-bottom:1px solid;font-size: 10px;font-weight: bold'>Campo</th>
                            <th style='text-align: left;border-right:1px solid;border-bottom:1px solid;font-size:10px;font-weight: bold'>Valor</th>
                            <th style='text-align: left;border-bottom:1px solid;font-size: 10px;font-weight: bold'>Campo</th>
                            <th style='text-align: left;border-bottom:1px solid;font-size: 10px;font-weight: bold'>Valor</th>
                          </tr>
                        </thead><tbody>";
                foreach($changes['after'] as $ck=>$vc){
                    $html .="<tr>
                                    <td style='padding:0px 8px 4px 0px;text-align: left;border-bottom:1px solid;'>".$changes['before'][$ck]['campo'].": </td>
                                    <td style='padding:0px 8px 4px 0px;text-align: left;border-right:1px solid;border-bottom:1px solid'>".($changes['before'][$ck]['valor'])."</td>
                                    <td style='padding:0px 8px 4px 0px;text-align: left;border-bottom:1px solid'>".$vc['campo'].": </td>
                                    <td style='padding:0px 8px 4px 0px;text-align: left;border-bottom:1px solid'>".($vc['valor'])."</td>
                                </tr>";
                }
                $html .="</tbody></table><br>";
            break;
            case 'simple':
                if(!empty($changes)) {
                    $html .="<table  class='full-width'>
                        <thead>
                          <tr>
                            <th colspan='2' style='text-align: center;font-size: 10px;font-weight: bold'>Informacion detallada del registro</th>
                          </tr>
                          <tr>
                            <th style='text-align: left;border-right:1px solid;border-bottom: 1px solid;font-size: 10px;font-weight: bold'>Campo</th>
                            <th style='text-align: left;border-bottom: 1px solid;font-size:10px;font-weight: bold'>Valor</th>            
                          </tr>
                        </thead><tbody>";
                    foreach ($changes as $ck => $vc) {
                        $html .= "<tr>
                                    <td style='padding:0px 8px 4px 0px;text-align: left;border-right:1px solid;border-bottom: 1px solid'>" . $changes[$ck]['campo'] . ": </td>
                                    <td style='padding:0px 8px 4px 0px;text-align: left;border-bottom: 1px solid'>" . ($changes[$ck]['valor']) . "</td>                                 
                                </tr>";
                    }
                    $html .= "</tbody></table>";
                }
            break;
        }
        return $html;
    }
    public function saveHistoryChangesServicios($servicioId,$initFactura='0000-00-00',$status,$costo,$personalId,$initOperaciones='0000-00-00',$nombrePersonal='Usuario interno',$lastDateWorkflow='0000-00-00'){
        global $personal;
	    $currentUser = $personal->getCurrentUser();
        $this->Util()->DB()->setQuery("
			INSERT INTO
				historyChanges
			(
				`servicioId`,
				`inicioFactura`,
				`status`,
				`costo`,
				`personalId`,
				`namePerson`,
				`inicioOperaciones`,
				lastDateWorkflow
		    )
		    VALUES
		    (
				'".$servicioId."',
				'".$initFactura."',
				'".$status."',
				'".$costo."',
				'".$currentUser["personalId"]."',
				'".$currentUser["name"]."',
				'".$initOperaciones."',
				'".$lastDateWorkflow."'
				
		    )");
        $this->Util()->DB()->InsertData();
    }
    function sendLogMultipleOperation($cambios = [],$contractId,$tipo='up',$actuales=[]){
        global $contract,$contractRep,$personal;
        if(empty($cambios))
            return false;


        $sql12 = "SELECT tipoServicio.departamentoId 
                FROM servicio
                INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
                WHERE servicioId IN(".implode(",", $cambios).")";

        $this->Util()->DB()->setQuery($sql12);
        $resultados =   $this->Util()->DB()->GetResult();

        $contract->setContractId($contractId);
        $contrato = $contract->Info();
        $encargados = $contractRep->encargadosArea($contractId);

        $ftr['incluirJefes'] = true;
        $ftr['sendBraun']= SEND_LOG_BRAUN;
        $ftr['sendHuerin']=SEND_LOG_HUERIN;
        if(count($resultados) > 0)
            $ftr['departamentos'] = array_column($resultados, 'departamentoId');

        //level es el nivel del rol, entre mayor es ,son mas bajos los privilegios si se pasa 0 se envia a todos
        $ftr['level'] = 5;
        $detalles= $contract->findEmailEncargadosJefesByContractId($ftr);
        $emails= $detalles['encargados'];
        $changes = $this->findHistoryLogServicio($cambios);
        $currentUser =  $personal->getCurrentUser();
        $this->Util()->Smarty()->assign("currentUser",$currentUser);
        $this->Util()->Smarty()->assign("contrato",$contrato);
        $this->Util()->Smarty()->assign("encargados",$encargados);
        $this->Util()->Smarty()->assign("servicios",$changes);
        $this->Util()->Smarty()->assign("DOC_ROOT",DOC_ROOT);
        switch($tipo){
            case 'up':
                $this->Util()->Smarty()->assign("tipo","up");
                $html =  $this->Util()->Smarty()->fetch(DOC_ROOT."/templates/molds/pdf-log-multiple-operation.tpl");
                $body =  $this->Util()->Smarty()->fetch(DOC_ROOT."/templates/molds/body-email-multiple.tpl");
            break;
            case 'new':
                $this->Util()->Smarty()->assign("tipo","new");
                $this->Util()->Smarty()->assign("actuales",$actuales);
                $html =  $this->Util()->Smarty()->fetch(DOC_ROOT."/templates/molds/pdf-log-add-multiple.tpl");
                $body =  $this->Util()->Smarty()->fetch(DOC_ROOT."/templates/molds/body-email-multiple.tpl");
            break;
        }
        $dompdf =  new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $fileName  = $_SESSION['User']['userId']."_log_multiple_operation.pdf";
        $output =  $dompdf->output();
        file_put_contents(DOC_ROOT."/sendFiles/$fileName", $output);

        if(!SEND_LOG_MOD) {
            $emails = [];
            $emails['hbcruz@braunhuerin.com.mx'] = "Dev Local";
        }

        $emails =  $this->verificarIgnorados($emails);

        $send =  new SendMail();
        $file = DOC_ROOT."/sendFiles/$fileName";
        $subject = PROJECT_STATUS === 'test' ? 'NOTIFICACION DE CAMBIOS EN TEST' : 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
        $send->PrepareMultipleNotice($subject,$body,$emails,"",$file,$fileName,"","","noreply@braunhuerin.com.mx","Administrador plataforma");
       // @unlink($file);
    }
    function sendLogUpdateServicios($cambios = []){

	    if(empty($cambios))
	        return false;

	    $changes = $this->findHistoryLogServicio($cambios);
	    $dompdf =  new Dompdf();
        $this->Util()->Smarty()->assign("servicios",$changes);
        $html =  $this->Util()->Smarty()->fetch(DOC_ROOT."/templates/molds/pdf-log-update-servicio.tpl");
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $fileName  = $_SESSION['User']['userId']."_log_servicio.pdf";
        $output =  $dompdf->output();
        file_put_contents(DOC_ROOT."/sendFiles/$fileName", $output);

        $send =  new SendMail();
        $file = DOC_ROOT."/sendFiles/$fileName";
        $subject = PROJECT_STATUS === 'test' ? 'NOTIFICACION DE CAMBIOS EN TEST' : 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
        $send->Prepare($subject,"Multiples cambios realizados, ver archivo adjunto.",EMAILCOORDINADOR,"COORDINADOR",$file,$fileName,"","","noreplye@braunhuerin.com.mx","Administrador plataforma");
        @unlink($file);
	}
     /*
     * encontrar el historial del elemento dado
     */
    function findHistoryLogServicio($servicios = []){
       $services = [];
      foreach($servicios as $servicio){
          $serv =  $this->findCompleteHistorial('servicio',$servicio,1);
          if($serv)
              $services[] = $serv;
      }
      return $services;
    }
    function findCompleteHistorial($table,$id,$n=1){
        switch($table){
            case 'servicio':
                $sql = "SELECT a.servicioId,c.contractId,c.name,a.inicioFactura,a.inicioOperaciones,a.status,a.costo,b.nombreServicio,a.lastDateWorkflow
                        FROM servicio a 
                        INNER JOIN tipoServicio b ON a.tipoServicioId=b.tipoServicioId and b.status='1'
                        INNER JOIN contract c ON a.contractId=c.contractId 
                        WHERE servicioId='$id' 
                       ";
                $this->Util()->DB()->setQuery($sql);
                $row = $this->Util()->DB()->GetRow();
                if(!empty($row))
                {
                    $servId = $row["servicioId"];

                    $sql2 = "SELECT  status FROM historyChanges WHERE servicioId = '$servId' ORDER BY fecha DESC";
                    $this->Util()->DB()->setQuery($sql2);
                    $lastMov= $this->Util()->DB()->GetSingle();
                    if($lastMov=='modificacion')
                        $limit =  " LIMIT 0,2";
                    else
                        $limit =  " LIMIT 1,1";

                    $sql = "SELECT  inicioOperaciones,costo,status,inicioFactura,namePerson,fecha,lastDateWorkflow FROM historyChanges WHERE servicioId = '$servId' ORDER BY fecha DESC $limit ";
                    $this->Util()->DB()->setQuery($sql);
                    $row['history'] = $this->Util()->DB()->GetResult();
                }
            break;
        }
        if(empty($row))
            return false;

        return $row;
    }
    function saveHistoryContract($conId,$status,$oldData,$newData){
        global $personal;
        $currentUser= $personal->getCurrentUser();
        $this->Util()->DB()->setQuery("
        INSERT INTO
        contractChanges(
            `contractId`,
            `status`,
            `oldData`,
            `newData`,
            `personalId`,
            `namePerson`
        )VALUES(
            '$conId',
            '$status',
            '".urlencode(serialize($oldData))."',
            '".urlencode(serialize($newData))."',
            '".$currentUser['personalId']."',
            '".$currentUser['name']."'
        );");
        $this->Util()->DB()->InsertData();
    }
    function saveHistoryCustomer($id,$oldData,$newData){
        global $personal;
        $currentUser= $personal->getCurrentUser();
        $this->Util()->DB()->setQuery("
			INSERT INTO	customerChanges
			(
				`customerId`,
				`status`,
				`oldData`,
				`newData`,
				`personalId`
		    )
		     VALUES
            (
                '" . $id. "',
                '" . $newData["active"] . "',
                '" . urlencode(serialize($oldData)) . "',
                '" . urlencode(serialize($newData)) . "',
                '" . $currentUser["personalId"] . "'
            );");
        $this->Util()->DB()->InsertData();
    }
    function sendPdfLogFromHtml($html = "") {
        global $personal;
        $html_complete = "<html>
                <head>
                <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                     <style type='text/css\'>
                        body {
                                font-family: helvetica, Sans-Serif;
                                font-size: 10px;
                                line-height: 1;
                            }
                        .full-width {
                            width: 100%;
                        }    
                     </style>
                </head>
            <body>";
        $html_complete .= $html;
        $html_complete .= "</body></html>";
        $current_user =  $personal->getCurrentUser();
        $dompdf =  new Dompdf();
        $html_complete = mb_convert_encoding($html_complete, 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html_complete);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $fileName  = $_SESSION['User']['userId']."_log_masive_update.pdf";
        $output =  $dompdf->output();
        $file1 = DOC_ROOT."/sendFiles/$fileName";
        file_put_contents($file1, $output);

        $subject = PROJECT_STATUS === 'test' ? 'NOTIFICACION DE CAMBIOS EN TEST' : 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
        $body ="<p>Se han realizado cambios masivos por el colaborador ".$current_user['name'].". </p>";
        $body .="<p>En el archivo adjunto encontrara la informacion detallada. </p>";

        if(is_file($file1)) {
            $sendmail = new SendMail();
            $sendmail->PrepareMultipleNotice($subject,$body,[],"",$file1,$fileName, "", "",'sistema@braunhuerin.com.mx','Administrador de plataforma');
            //unlink($file1);
        }
    }

    public function verificarIgnorados(array $encargados) {

        $emailIgnorados = explode(",", EMAIL_IGN_NOT_CAMBIO);
        if(count($emailIgnorados) <= 0)
            return $encargados;

        $new = [];
        foreach($encargados as $key => $val) {
            if(in_array($key, $emailIgnorados) !== false)
                continue;

            $new[$key] = $val;
        }
        return $new;
    }
}//Log
