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
        if ($this->Util()->ValidateRequireField($value, "Aviso"))
            $this->description = $value;
    }

    public function setPrioridad($value)
    {
        if ($this->Util()->ValidateRequireField($value, "Prioridad"))
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
        $filter = " and fecha >= (date_add(curdate(), interval -1 month) - interval dayofmonth(date_add(curdate(), interval -1 month))-1 day) and fecha <= curdate() ";
        global $User, $rol;
        $this->Util()->DB()->setQuery("SELECT COUNT(*) FROM notice WHERE status='vigente' $filter ");
        $total = $this->Util()->DB()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total, WEB_ROOT . "/homepage");

        $sql_add = "LIMIT " . $pages["start"] . ", " . $pages["items_per_page"];
        $this->Util()->DB()->setQuery("SELECT * FROM notice WHERE status='vigente' $filter ORDER BY noticeId DESC " . $sql_add);
        $result = $this->Util()->DB()->GetResult();

        //comprobar si el usuario esta permitido que ve el aviso
        foreach ($result as $key => $value) {
            if ((int)$User["level"] != 1) {
                $this->Util()->DB()->setQuery('SELECT * FROM noticeOwners WHERE noticeId="' . $value['noticeId'] . '" ');
                $res = $this->Util()->DB()->GetResult();
                $owners = array();
                foreach ($res as $itm)
                    $owners[$itm['departamentoId']] = explode(',', $itm['roles']);

                // comprobar a que area pertenece el usuario activo para mostrar o no
                $rol->setTitulo($User['tipoPersonal']);
                $rolId = $rol->GetIdByName();
                $roleId = $rolId <= 0 ? $User['roleId'] : $rolId;
                if ($roleId) {
                    $rol->setRolId($roleId);
                    $infoRol = $rol->Info();
                }
                $depId = $infoRol['departamentoId'];
                // si el rol de usuario esta permitido que lo vea lo vera
                if (empty($owners)) {
                    //si permisos del aviso es vacio, y fecha del aviso es apartir del 17042018 se elimina por que debe tener permiso
                    if ($value['fecha'] >= '2018-04-17')
                        unset($result[$key]);

                    continue;
                }
                if (!array_key_exists($depId, $owners))
                    unset($result[$key]);
                elseif (!in_array($roleId, $owners[$depId]))
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
					noticeId = '" . $this->noticeId . "'";

        $this->Util()->DB()->setQuery($sql);
        $info = $this->Util()->DB()->GetRow();

        $row = $this->Util->EncodeRow($info);

        return $row;
    }

    public function GetIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return 0;
    }

    function CheckIfSelectedArea($required)
    {
        global $rol;
        $res = $rol->GetRolesGroupByDep();
        $owners = array();
        foreach ($res as $key => $value) {
            //si el departamento esta seleccionado comprobar si por lo menos uno de sus roles esta seleccionado
            if ($_POST['dep-' . $value['departamentoId']]) {
                if (!empty($_POST['roles-' . $value['departamentoId']]))//si esta seleccionado al menos un rol del departamento se agrega de lo contrario no.
                    $owners[$value['departamentoId']] = $_POST['roles-' . $value['departamentoId']];
            }
        }
        if (empty($owners)) {
            if (!$required)
                $this->Util()->setError(10001, 'error', 'Es necesario seleccionar por lo menos una area');
            return false;
        } else {
            return $owners;
        }

    }

    public function Save()
    {
        global $rol, $customer, $User;
        //comprobar que se ha seleccionado  por lo menos una area
        $owners = $this->CheckIfSelectedArea($this->sendCustomer);
        if ($this->Util()->PrintErrors()) {
            return false;
        }
        if ($this->sendCustomer)
            $enviadoCliente = "Si";
        else
            $enviadoCliente = "No";

        $ip = $this->GetIp();
        $sqlQuery = "INSERT INTO 
					notice 
					(
						usuario,										
						fecha,
						description,
						priority,
                        ip,
                        sendCustomer
					)
				 VALUES 
					(			
						'" . $this->usuario . "',			
						'" . $this->fecha . "',
						'" . trim($this->description) . "',
						'" . $this->prioridad . "',
                        '" . $ip . "',
                        '" . $enviadoCliente . "'
					)";

        $this->Util()->DB()->setQuery($sqlQuery);
        $noticeId = $this->Util()->DB()->InsertData();
        //guardar los permisos
        if ($noticeId && !empty($owners)) {
            $sqlOwn = "INSERT INTO noticeOwners VALUES";
            foreach ($owners as $ko => $vo) {
                $rls = implode(",", $vo);

                if ($vo == end($owners))
                    $sqlOwn .= "(" . $noticeId . "," . $ko . ",'" . $rls . "');";
                else
                    $sqlOwn .= "(" . $noticeId . "," . $ko . ",'" . $rls . "'),";
            }
        }
        $ruta = DOC_ROOT . '/archivos';
        $archivo = $_FILES["path"]['name'];
        $extension = explode(".", $archivo);
        $doUpload = false;
        $destino = "";
        $fileName = "";
        if ($_FILES["path"]['name'] && $_FILES["path"]['error'] === 0 && $noticeId) {
            $prefijo = "boletin_braunhuerin_" . $noticeId;
            $fileName = $prefijo . "." . end($extension);
            $destino = $ruta . "/" . $fileName;
            if (move_uploaded_file($_FILES['path']['tmp_name'], $destino)) {
                $sql = "UPDATE notice SET url =  '" . utf8_decode($fileName) . "' WHERE noticeId = " . $noticeId;
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->ExecuteQuery();
                $doUpload = true;
            }
        } elseif ($noticeId) {
            $doUpload = true;
        }
        if (!$doUpload) {
            $this->Util()->DB()->RollBackRegister('notice', 'noticeId', $noticeId);
            $this->Util()->setError(0, 'error', 'Hubo un error intentelo de nuevo');
            $this->Util()->PrintErrors();
            return false;
        } else {
            if (!empty($owners)) {
                $this->Util()->DB()->setQuery($sqlOwn);
                $this->Util()->DB()->ExecuteQuery();
                $this->Util()->DB()->CleanQuery();
                $sqlQuery = "SELECT * FROM personal WHERE active='1' ";
                $this->Util()->DB()->setQuery($sqlQuery);
                $personal = $this->Util()->DB()->GetResult();
                // quitar a huerin
                if(!SEND_LOG_HUERIN) {
                    $personal = !is_array($personal) ? [] : $personal;
                    $personalLine =  array_column($personal, 'personalId');
                    $personalLine = !is_array($personalLine) ? [] : $personalLine;
                    $keyFind = array_search(IDHUERIN, $personalLine);
                    if($keyFind !== false)
                        unset($personal[$keyFind]);
                }
                $subject = "AVISO  NUEVO DE " . $this->usuario;
                $sendmail = new SendMail();
                $mails = array();
                foreach ($personal as $key => $usuario) {
                    // comprobar a que area pertenece el rol del personal
                    $rol->setTitulo($usuario['tipoPersonal']);
                    $rolId = $rol->GetIdByName();
                    $roleId = $rolId <= 0 ? $usuario['roleId'] : $rolId;
                    if ($roleId) {
                        $rol->setRolId($roleId);
                        $infoRol = $rol->Info();
                    }
                    $depId = !$infoRol['departamentoId'] ? $usuario['departamentoId'] : $infoRol['departamentoId'];
                    if ((array_key_exists($depId, $owners) && in_array($roleId, $owners[$depId])) || ($usuario['tipoPersonal'] == 'Socio' || $usuario['tipoPersonal'] == 'Coordinador'))
                        if ($this->Util()->ValidateEmail($usuario['email']))
                            $mails[$usuario['email']] = $usuario['name'];

                }

                $body = "<pre> " . nl2br(utf8_decode($this->description));
                $body .= "<br><br>Aviso creado por " . $this->usuario;
                $adjuntos = [];
                if (file_exists($destino)) {
                    $cad['name'] = $fileName;
                    $cad['url'] =  $destino;
                    array_push($adjuntos, $cad);
                    $body .= "<br><br>El aviso tiene un archivo que puedes descargar dentro del sistema";
                }
                $sendmail->SendMultipleNotice($subject, $body, $mails, $adjuntos, 'noreply@braunhuerin.com.mx', 'AVISO DE PLATAFORMA', true);
            }
        }

        if ($this->sendCustomer) {
            $User['userId'] = 0;
            $customers = $customer->EnumerateOptimizado();
            $clientesCorreos = array();
            foreach ($customers as $cm => $vm) {
                if (empty($vm['contracts'])) {
                    continue;
                }
                $clientesCorreos = array_merge($clientesCorreos, $vm['allEmails']);
            }
            $subject = "BRAUN HUERIN INFORMA";
            $body = '' . nl2br(utf8_decode($this->description));
            $adjuntos = [];
            if (file_exists($destino)) {
                $cad['name'] = $fileName;
                $cad['url'] =  $destino;
                array_push($adjuntos, $cad);
                $body .= "<br><br> Revisar archivo adjunto, Gracias!!";
            }
            //desactivar asta que confime rogelio
            $sendmail = new SendMail();
            $sendmail->SendMultipleNotice($subject, $body, $clientesCorreos, $adjuntos, 'noreply@braunhuerin.com.mx', 'BRAUN HUERIN', false, true);
        }
        $this->Util()->setError(0, 'complete', 'El aviso se ha agregado correctamente');
        $this->Util()->PrintErrors();
        return true;
    }


    public function Update()
    {
        if ($this->Util()->PrintErrors()) {
            return false;
        }

        $sql = "UPDATE 
					notice 
				SET 
					url =  '" . utf8_decode($this->dir) . "'									
				WHERE 
					noticeId = " . $this->noticeId;

        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->ExecuteQuery();
        return true;

    }

    public function Delete()
    {

        if ($this->Util()->PrintErrors()) {
            return false;
        }

        $sql = "DELETE FROM 
					notice
				WHERE 
					noticeId = " . $this->noticeId;

        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->ExecuteQuery();

        $this->Util()->setError(21043, "complete");
        $this->Util()->PrintErrors();

        return true;

    }

    public function GetNameById()
    {

        $sql = 'SELECT 
					name
				FROM 
					city 
				WHERE 
					cityId = ' . $this->cityId;

        $this->Util()->DB()->setQuery($sql);

        return $this->Util()->DB()->GetSingle();

    }

}//Notice

?>
