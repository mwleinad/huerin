<?php

class Notice extends Main
{
	private $usuario;
	private $description;	
	private $fecha;	
	private $noticeId;
	private $prioridad;
	private $dir;
	
	public function setUsuario($value)
	{
		$this->usuario = $value;
	}
	public function setNoticeId($value)
	{
		$this->noticeId = $value;
	}
	public function setPath($value)
	{
		$this->dir = $value;
	}			
	public function setDescription($value)
	{
		if($this->Util()->ValidateRequireField($value, "Aviso"))
		$this->description = $value;
	}
	public function setPrioridad($value)
	{
		if($this->Util()->ValidateRequireField($value, "Prioridad"))
		$this->prioridad = $value;
	}
	public function setFecha($value)
    {
        $this->fecha = $value;
    }
    private $sendCustomer;
    public function setSendCustomer($value)
    {
        $this->sendCustomer = $value;
    }
	public function Enumerate()
	{
	    global $User,$rol;
	    $this->Util()->DB()->setQuery('SELECT COUNT(*) FROM notice');
		$total = $this->Util()->DB()->GetSingle();

		$pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/homepage");

		$sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
		$this->Util()->DB()->setQuery('SELECT * FROM notice ORDER BY noticeId DESC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();
		//comprobar si el usuario esta permitido que ve el aviso
        foreach($result as $key => $value){
            if($User['tipoPersonal']!='Admin'&&$User['tipoPersonal']!='Socio'&&$User['tipoPersonal']!='Coordinador'){
                $this->Util()->DB()->setQuery('SELECT * FROM noticeOwners WHERE noticeId="'.$value['noticeId'].'" ');
                $res  = $this->Util()->DB()->GetResult();
                $owners =array();
                foreach($res as $itm)
                    $owners[$itm['departamentoId']]=explode(',',$itm['roles']);

                // comprobar a que area pertenece el usuario activo para mostrar o no
                $rol->setTitulo($User['tipoPersonal']);
                $rolId=$rol->GetIdByName();
                $roleId = $rolId<=0?$User['roleId']:$rolId;
                if($roleId)
                {
                    $rol->setRolId($roleId);
                    $infoRol = $rol->Info();
                }
                $depId = $infoRol['departamentoId'];
                // si el rol de usuario esta permitido que lo vea o que sea el coordinador o socio le llegara
                if(empty($owners))
                {
                    continue;
                }
                if(!array_key_exists($depId,$owners))
                    unset($result[$key]);
                elseif(!in_array($roleId,$owners[$depId]))
                    unset($result[$key]);



            }

        }
		$data["items"] = $result;
		$data["pages"] = $pages;
		return $data;	
	}
	public function GetLast()
	{		
								
		$sql = "SELECT 
					MAX(noticeId)
				FROM 
					notice";
		
		$this->Util()->DB()->setQuery($sql);
		$single = $this->Util()->DB()->GetSingle();
				
		return $single;
	}
		
	public function Info()
	{
		
		$sql = "SELECT 
					* 
				FROM 
					notice 
				WHERE 
					noticeId = '".$this->noticeId."'";
	
		$this->Util()->DB()->setQuery($sql);
		$info = $this->Util()->DB()->GetRow();
		
		$row = $this->Util->EncodeRow($info);
				
		return $row;
	}
        
    public function GetIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return 0;
    }
    function CheckIfSelectedArea(){
        global $rol;
        $res = $rol->GetRolesGroupByDep();
        $owners = array();
        foreach($res as $key => $value){
            //si el departamento esta seleccionado comprobar si por lo menos uno de sus roles esta seleccionado
            if($_POST['dep-'.$value['departamentoId']]){
                if(!empty($_POST['roles-'.$value['departamentoId']]))//si esta seleccionado al menos un rol del departamento se agrega de lo contrario no.
                    $owners[$value['departamentoId']]=$_POST['roles-'.$value['departamentoId']];
            }
        }
        if(empty($owners)){
            $this->Util()->setError(10001,'error','Es necesario seleccionar por lo menos una area');
            return false;
        }else{
            return $owners;
        }

    }
    public function Save(){
        global $rol,$customer,$User;
        //comprobar que se ha seleccionado  por lo menos una area
        $owners = $this->CheckIfSelectedArea();
		if($this->Util()->PrintErrors()){
			return false; 
		}
        $ip = $this->GetIp();
		$sqlQuery = "INSERT INTO 
					notice 
					(
						usuario,										
						fecha,
						description,
						priority,
                        ip
					)
				 VALUES 
					(			
						'".$this->usuario."',			
						'".$this->fecha."',
						'".$this->description."',
						'".$this->prioridad."',
                        '".$ip."'
					)";
								
		$this->Util()->DB()->setQuery($sqlQuery);
		$noticeId = $this->Util()->DB()->InsertData();
        //guardar los permisos
		if($noticeId&&!empty($owners)) {
            $sqlOwn = "INSERT INTO noticeOwners VALUES";
            foreach ($owners as $ko => $vo) {
                $rls = implode(",", $vo);

                if ($vo == end($owners))
                    $sqlOwn .= "(" . $noticeId . "," . $ko . ",'" . $rls . "');";
                else
                    $sqlOwn .= "(" . $noticeId . "," . $ko . ",'" . $rls . "'),";
            }
        }
	   $ruta = DOC_ROOT.'/archivos';
	   $archivo = $_FILES["path"]['name'];
	   $extension = explode(".",$archivo);
	   $doUpload = false;
       $destino ="";
       if($_FILES["path"]['name']&&$_FILES["path"]['error']===0&&$noticeId)
        {
            $prefijo = "notice_".$_POST["usuario"].$noticeId;
            $fileName = $prefijo.".".end($extension);
            $destino =  $ruta."/".$fileName;
            if(move_uploaded_file($_FILES['path']['tmp_name'],$destino))
            {
                //si el archivo se almaceno correctamente se actualiza el path+
                $sql = "UPDATE notice SET url =  '".utf8_decode($fileName)."' WHERE noticeId = ".$noticeId;
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->ExecuteQuery();
                $doUpload= true;
            }
        }elseif($noticeId){
           $doUpload=true;
       }
        if(!$doUpload){
           $this->Util()->DB()->RollBackRegister('notice','noticeId',$noticeId);
           $this->Util()->setError(0,'error','Hubo un error intentelo de nuevo');
           $this->Util()->PrintErrors();
           return false;
        }else{

            //si el archivo se subio correctamente se procede a guardar los permisos y enviar por correo de lo contrario se realiza un rollback
            $this->Util()->DB()->setQuery($sqlOwn);
            $this->Util()->DB()->ExecuteQuery();
            $this->Util()->DB()->CleanQuery();

            $sqlQuery = "SELECT * FROM personal WHERE personalId != '".IDBRAUN."'";
            $this->Util()->DB()->setQuery($sqlQuery);
            $personal = $this->Util()->DB()->GetResult();
            $subject = "Aviso Nuevo ".$this->usuario;
            $sendmail = new SendMail();
            $mails = array();
            foreach($personal as $key => $usuario)
            {
                // comprobar a que area pertenece el rol del personal
                $rol->setTitulo($usuario['tipoPersonal']);
                $rolId=$rol->GetIdByName();
                $roleId = $rolId<=0?$usuario['roleId']:$rolId;
                if($roleId)
                {
                    $rol->setRolId($roleId);
                    $infoRol = $rol->Info();
                }
                $depId = !$infoRol['departamentoId']?$usuario['departamentoId']:$infoRol['departamentoId'];
                 // si el rol de usuario esta permitido que lo vea o que sea el coordinador o socio le llegara
                if((array_key_exists($depId,$owners)&&in_array($roleId,$owners[$depId]))||($usuario['tipoPersonal']=='Socio'||$usuario['tipoPersonal']=='Coordinador'))
                    $mails[$usuario['email']] = $usuario['name'];

            }
            $body = "<pre> ".nl2br(utf8_decode($this->description));
            $body .= "<br><br>El aviso fue creado por ".$this->usuario;
            if(file_exists($destino))
            {
                $body .= "<br><br>El aviso tiene un archivo que puedes descargar dentro del sistema";
            }

            $sendmail->PrepareMultiple($subject, $body, $mails, '', $destino, $fileName, "", "");
            //si se selecciona enviar a cliente hacer lo siguiente
            if($this->sendCustomer){
                //administrador,socio y coordinador pueden seleccionar enviar a cliente
                //que se obtengan todos los clientes
                $User['userId'] = 0;
                $customers = $customer->Enumerate();
                $clientes =array();
                $clientesCorreos = array();
                foreach($customers as $cm=>$vm){
                    if(empty($vm['contracts'])){
                        continue;
                    }
                    foreach($vm['contracts'] as $cr=>$vr){
                        if($vr['activo']=="Si"){
                            array_push($clientes,$vm['customerId']);
                            if($this->Util()->ValidateEmail(trim($vr['emailContactoAdministrativo'])))
                                $clientesCorreos[trim($vr['emailContactoAdministrativo'])]=$vr['nombreComercial'];
                            if($this->Util()->ValidateEmail(trim($vr['emailContactoDirectivo'])))
                                $clientesCorreos[trim($vr['emailContactoDirectivo'])]=$vr['nombreComercial'];
                            break;
                        }
                    }
                }
                //enviar correo al cliente
                $subject ="BOLETIN INFORMATIVO";
                $body ='<pre>Despcripcion del aviso <br><br>'.nl2br(utf8_decode($this->description));
                if(file_exists($destino))
                {
                    $body .= "<br><br> Revisar archivo adjunto, Gracias!!";
                }
                // $sendmail->PrepareMultiple($subject, $body, $clientesCorreos, '', $destino, $fileName, "", "");
            }
        }
        $this->Util()->setError(0,'complete','El aviso se ha agregado correctamente');
        $this->Util()->PrintErrors();
		return true;
	}
	
	public function Update(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "UPDATE 
					notice 
				SET 
					url =  '".utf8_decode($this->dir)."'									
				WHERE 
					noticeId = ".$this->noticeId;

		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
		return true;
				
	}
	
	public function Delete(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "DELETE FROM 
					notice
				WHERE 
					noticeId = ".$this->noticeId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
										
		$this->Util()->setError(21043, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
		
	public function GetNameById(){
			
		$sql = 'SELECT 
					name
				FROM 
					city 
				WHERE 
					cityId = '.$this->cityId;
		
		$this->Util()->DB()->setQuery($sql);
		
		return $this->Util()->DB()->GetSingle();
		
	}	
	
}//Notice

?>