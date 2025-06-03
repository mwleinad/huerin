<?php

class Inventory extends Articulo
{
    private $nameReport;

    public function setNameReport ($value) {
        $this->nameReport = $value;
    }
    public function getNameReport()
    {
        return $this->nameReport;
    }

    public function getConsecutiveIdResource()
    {
        $this->Util()->DB()->setQuery("select max(office_resource_id)+1 from office_resource");
        return $this->Util()->DB()->GetSingle();
    }

    public function validateResponsableIfExist()
    {
        if ($this->getResponsableResourceId())
            $sql = "select * from responsables_resource_office WHERE office_resource_id = '" . $this->getId() . "' and personalId = '" . $this->getPersonalId() . "' and responsable_resource_id != '" . $this->getResponsableResourceId() . "' and status = 'Activo'";
        else
            $sql = "select * from responsables_resource_office WHERE office_resource_id = '" . $this->getId() . "' and personalId = '" . $this->getPersonalId() . "' and status = 'Activo' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        if ($row)
            $this->Util()->setError(0, "error", "El responsable seleccionado ya se encuentra relacionado con el registro actual");
    }

    public function validateFileResponsiva($required = true)
    {
        if (!isset($_FILES['responsiva']))
            return false;

        if ($_FILES['responsiva']['error'] == 0) {
            if ($_FILES['responsiva']['size'] > 2 * MB) {
                $this->Util()->setError(0, "error", "Archivo de responsiva excede el limite maximo permitido: 2MB");
                return false;
            }

            if (strtoupper(end(explode('.', $_FILES["responsiva"]['name']))) != 'PDF') {
                $this->Util()->setError(0, "error", "Archivo de responsiva formato invalido");
                return false;
            }
        } elseif ($required) {
            $this->Util()->setError(0, "error", "Es necesario adjuntar responsiva");
            return false;
        }

        return true;
    }

    public function saveResource()
    {
        if ($this->Util()->PrintErrors())
            return false;
        $vencimiento = $this->Util()->isValidateDate($this->getFechaVencimiento(), 'Y-m-d') ? "'".$this->getFechaVencimiento()."'" : 'null';
        $sql = "INSERT INTO office_resource(
                            nombre,
                            descripcion,
                            tipo_recurso,
                            no_serie,
                            no_licencia,
                            codigo_activacion,
                            vencimiento,
                            fecha_compra,
                            costo_compra,
                            costo_recuperacion,
                            tipo_equipo,
                            tipo_dispositivo,
                            tipo_software,
                            marca,
                            modelo,
                            procesador,
                            memoria_ram,
                            disco_duro,
                            no_inventario,
                            status,
                            usuario_alta,
                            fecha_alta) VALUES(
                              '" . $this->getNombre() . "',
                              '" . $this->getDescripcion() . "',
                              '" . $this->getTipoRecurso() . "',
                              '" . $this->getNoSerie() . "',
                              '" . $this->getNoLicencia() . "',
                              '" . $this->getCodigoActivacion() . "',
                              ".$vencimiento.",
                              '" . $this->getFechaCompra() . "',
                              '" . $this->getCostoCompra() . "',
                              '" . $this->getCostoRecuperacion() . "',
                              '" . $this->getTipoEquipo() . "',
                              '" . $this->getTipoDispositivo() . "',
                              '" . $this->getTipoSoftware() . "',
                              '" . $this->getMarca() . "',
                              '" . $this->getModelo() . "',
                              '" . $this->getProcesador() . "',
                              '" . $this->getMemoriaRam() . "',
                              '" . $this->getDiscoDuro() . "',
                              '" . $this->getNoInventario() . "',
                              'Activo',
                              '" . $_SESSION['User']['username'] . "',
                              '" . date("Y-m-d H:i:s") . "'             
                            )";
        $this->Util()->DB()->setQuery($sql);
        $lastId = $this->Util()->DB()->InsertData();
        $this->syncDeviceToKit($lastId);
        $this->syncSoftwareToResource($lastId);
        $this->Util()->setError(0, "complete", "El registro se ha guardado correctamente.");
        $this->Util()->PrintErrors();
        return true;
    }

    public function updateResource()
    {
        if ($this->Util()->PrintErrors())
            return false;
        $actualizables = "";
        if(in_array($this->getTipoRecurso(), ['Computadora']))
            $actualizables .= "no_inventario = '" . $this->getNoInventario() . "',";
        $vencimiento = $this->Util()->isValidateDate($this->getFechaVencimiento(), 'Y-m-d') ? "'".$this->getFechaVencimiento()."'" : 'null';
        $sql = " UPDATE office_resource SET
                    nombre = '" . $this->getNombre() . "',
                    descripcion = '" . $this->getDescripcion() . "',
                    usuario_modificacion = '" . $_SESSION["User"]["username"] . "',
                    tipo_recurso =  '" . $this->getTipoRecurso() . "',
                    no_serie = '" . $this->getNoSerie() . "',
                    no_licencia = '" . $this->getNoLicencia() . "',
                    codigo_activacion = '" . $this->getCodigoActivacion() . "',
                    vencimiento = ".$vencimiento.",
                    fecha_compra = '" . $this->getFechaCompra() . "',
                    costo_compra = '" . $this->getCostoCompra() . "',
                    costo_recuperacion = '" . $this->getCostoRecuperacion() . "',
                    tipo_equipo = '" . $this->getTipoEquipo() . "',
                    tipo_dispositivo = '" . $this->getTipoDispositivo() . "',
                    tipo_software = '" . $this->getTipoSoftware() . "',
                    marca = '" . $this->getMarca() . "',
                    modelo = '" . $this->getModelo() . "',
                    procesador = '" . $this->getProcesador() . "',
                    memoria_ram = '" . $this->getMemoriaRam() . "',
                    disco_duro = '" . $this->getDiscoDuro() . "',
                    ".$actualizables."
                    fecha_ultima_modificacion = '" . date("Y-m-d H:i:s") . "' 
                    WHERE office_resource_id = '" . $this->getId() . "'
                ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
        $this->syncDeviceToKit($this->getId());
        $this->syncSoftwareToResource($this->getId());
        $this->Util()->setError(0, "complete", "El registro se ha actualizado correctamente.");
        $this->Util()->PrintErrors();
        return true;
    }

    public function saveResponsablesResource()
    {
        $this->validateResponsableIfExist();
        if ($this->Util()->PrintErrors())
            return false;

        if ($this->getResponsableResourceId()) {
            $sql = "UPDATE responsables_resource_office
                            SET 
                            personalId = '" . $this->getPersonalId() . "',
                            fecha_entrega_responsable = '" . $this->getFechaEntregaResponsable() . "',
                            tipo_responsable = '" . $this->getTipoResponsable() . "',
                            usuario_modificador = '" . $_SESSION['User']['username'] . "',
                            fecha_ultima_modificacion = '" . date("Y-m-d H:i:s") . "'
                            WHERE responsable_resource_id = '" . $this->getResponsableResourceId() . "'
                                 
                        ";
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->UpdateData();
            $this->Util()->setError(0, "complete", "Se ha actualizado un registro");
        } else {
            $sql = "INSERT INTO responsables_resource_office(
                             office_resource_id,
                             personalId,
                             fecha_entrega_responsable,
                             tipo_responsable,
                             usuario_creador,
                             status,
                             fecha_creacion
                             )values(
                              '" . $this->getId() . "',
                              '" . $this->getPersonalId() . "',
                              '" . $this->getFechaEntregaResponsable() . "',
                              '" . $this->getTipoResponsable() . "',
                              '" . $_SESSION['User']['username'] . "',
                              'Activo',
                              '" . date("Y-m-d H:i:s") . "'       
                             )
                            ";
            $this->Util()->DB()->setQuery($sql);
            $last_res_id = $this->Util()->DB()->InsertData();
            $this->Util()->setError(0, "complete", "Se ha guardado un registro");
            $this->setResponsableResourceId($last_res_id);
        }
        if ($ruta = $this->saveResponsiva($_FILES)) {
            $sql = "update responsables_resource_office set responsiva_root = '$ruta' where responsable_resource_id = '" . $this->getResponsableResourceId() . "' ";
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->UpdateData();
        }
        $this->Util()->PrintErrors();
        return true;
    }

    function saveResponsiva($FILES, $down = false)
    {
        $base_ruta = "/expedientes/" . $this->getPersonalId() . "/responsivas";
        $folder = DOC_ROOT . $base_ruta;
        if ($FILES['responsiva']['error'] == 0) {
            $ext = end(explode('.', $FILES['responsiva']['name']));

            $name = $down ? $this->getId() . "_file_responsiva_baja_" . $this->getResponsableResourceId() . ".$ext" : $this->getId() . "_file_responsiva_" . $this->getResponsableResourceId() . ".$ext";
            if (!is_dir($folder))
                mkdir($folder, 0777, true);
            if (move_uploaded_file($FILES['responsiva']['tmp_name'], $folder . "/$name")) {
                unset($_FILES['responsiva']);
                return $base_ruta . "/$name";
            } else return false;
        } else
            return false;
    }

    public function saveDeleteResponsable()
    {
        if ($this->Util()->PrintErrors())
            return false;

        $info = $this->infoResponsableResource();
        $this->setPersonalId($info['personalId']);
        if ($ruta = $this->saveResponsiva($_FILES, true)) {
            $sql = "UPDATE responsables_resource_office 
                   SET status = 'Baja',
                   motivo_baja_responsable = '" . $this->getMotivoBaja() . "',    
                   responsiva_baja_root = '$ruta',
                   fecha_liberacion_responsable='" . date('Y-m-d H:i:s') . "' 
                    WHERE responsable_resource_id = '" . $this->getResponsableResourceId() . "' ";
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->UpdateData();
            $this->Util()->setError(0, "complete", "Has eliminado correctamente un registro");
            $res = true;
        } else {
            $this->Util()->setError(0, "error", "Ocurrio un error al dar de baja, intentelo de nuevo");
            $res = false;
        }
        $this->Util()->PrintErrors();
        return $res;
    }

    function getListResponsablesResource($id, $incluirBaja = false)
    {

        $filtro = "";
        if (!$incluirBaja)
            $filtro .= " and a.status ='Activo' ";

        $sql = "select a.*,
                (select departamento from departamentos where departamentoId = b.departamentoId limit 1) departamento,
                b.name as nombre, 
                b.email, 
                b.mailGrupo,
                b.userComputadora,
                b.passwordComputadora,
                b.userAspel,
                b.passwordAspel
                from responsables_resource_office a
                INNER JOIN personal b ON a.personalId = b.personalId
                WHERE a.office_resource_id = '" . $id . "' $filtro order by a.responsable_resource_id desc, a.fecha_entrega_responsable desc ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }

    public function infoResource()
    {
        $sql = "select * from office_resource where office_resource_id = '" . $this->getId() . "'  ";
        $this->Util()->DB()->setQuery($sql);
        $info = $this->Util()->DB()->GetRow();
        if ($info) {
            $responsables = $this->getListResponsablesResource($info["office_resource_id"], true);
            if ($responsables) {
                $info["responsables"] = $responsables;
            }
            $devices = $this->getDeviceResource($info['no_inventario']);
            if ($devices) {
                $info["device_resource"] = $devices;
            }

            $softwares = $this->getSoftwareResource($info['no_inventario']);
            if ($softwares) {
                $info["software_resource"] = $softwares;
            }

        }
        return $info;
    }

    public function basicInfoResource()
    {
        $sql = "select * from office_resource where office_resource_id = '" . $this->getId() . "'  ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetRow();
    }

    public function infoResponsableResource()
    {
        $sql = "SELECT a.*,b.name as nombre from responsables_resource_office a
                INNER JOIN personal b ON a.personalId=b.personalId
                WHERE a.responsable_resource_id = '" . $this->getResponsableResourceId() . "'  ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetRow();
    }

    private function getDeviceResource($noInventario)
    {
        $sql= "select * from office_resource where tipo_recurso = 'Accesorios' AND no_inventario = '" . $noInventario . "' AND fecha_baja is null ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
    private function getSoftwareResource($noInventario)
    {
        $sql= "select * from office_resource where tipo_recurso = 'Sistemas' AND no_inventario = '" . $noInventario . "' AND fecha_baja is null ";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        foreach($result as $key => $var)
            $result[$key]['dataVencimiento'] = $this->calculateVencimientoSoftware($var);
        return $result;
    }

    public function enumerateResource()
    {
        /*$this->Util()->DB()->setQuery("SELECT COUNT(*) FROM office_resource WHERE status='Activo'");

        $total = $this->Util()->DB()->GetSingle();
        $pages = $this->Util->HandleMultipages($this->page, $total, WEB_ROOT . "/resource-office");
        $sql_add = "LIMIT " . $pages["start"] . ", " . $pages["items_per_page"];*/

        $this->Util()->DB()->setQuery('SELECT * FROM office_resource ORDER BY no_inventario DESC, office_resource_id ASC' . $sql_add);
        $result = $this->Util()->DB()->GetResult();

        foreach ($result as $key => $var) {
            $result[$key]["responsables"] = $this->getListResponsablesResource($var["office_resource_id"]);
            $this->setId($var['office_resource_id']);
            $result[$key]["upkeeps"] = $this->enumerateUpKeeps(true);
        }


        $data["items"] = $result;
        //$data["pages"] = $pages;

        return $data;
    }

    public function listResource()
    {
        $this->Util()->DB()->setQuery("SELECT office_resource_id, marca,modelo,no_serie,tipo_equipo FROM office_resource ORDER BY nombre ASC ");
        return $this->Util()->DB()->GetResult();
    }

    public function listResourceInStock($type = 'Accesorios')
    {
        $this->Util()->DB()->setQuery("SELECT office_resource_id, marca, modelo,no_serie, tipo_dispositivo, tipo_software, no_licencia, codigo_activacion 
                                              FROM office_resource 
                                              WHERE tipo_recurso= '".$type."'  and (no_inventario='' or no_inventario is null)
                                              ORDER BY office_resource_id ASC ");
        return $this->Util()->DB()->GetResult();
    }

    public function searchResource()
    {
        $filtro = "";

        $like = $_POST['name_descripcion'];
        $responsable = $_POST['responsable'];
        $tipoRecurso = 'Computadora';
        $finit = $_POST['fecha_alta_inicio'];
        $fend = $_POST['fecha_alta_fin'];
        $fcinit = $_POST['fecha_compra_inicio'];
        $fcend = $_POST['fecha_compra_fin'];

        if (strlen($like) > 0)
            $filtro .= " and (a.nombre like '%$like%' OR a.descripcion like '%$like%') ";

        if (strlen($responsable) > 0)
            $filtro .= " and b.nombre like '%$responsable%' ";
        if ($tipoRecurso != "")
            $filtro .= " and a.tipo_recurso = '$tipoRecurso' ";
        if ($this->Util()->isValidateDate($finit, "d-m-Y"))
            $filtro .= " and date(a.fecha_alta) >= '" . $this->Util()->FormatDateMySql($finit) . "' ";
        if ($this->Util()->isValidateDate($fend, "d-m-Y"))
            $filtro .= " and date(a.fecha_alta) <= '" . $this->Util()->FormatDateMySql($fend) . "' ";
        if ($this->Util()->isValidateDate($fcinit, "d-m-Y"))
            $filtro .= " and a.fecha_compra >= '" . $this->Util()->FormatDateMySql($fcinit) . "' ";
        if ($this->Util()->isValidateDate($fcend, "d-m-Y"))
            $filtro .= " and a.fecha_compra <= '" . $this->Util()->FormatDateMySql($fcend) . "' ";

        $sql = "SELECT count(DISTINCT(a.office_resource_id)) FROM office_resource a 
                 LEFT JOIN (SELECT c.office_resource_id,d.name AS nombre,c.status FROM responsables_resource_office c INNER JOIN personal d ON c.personalId=d.personalId  WHERE c.status ='Activo') b ON a.office_resource_id = b.office_resource_id
                 WHERE 1 $filtro";

        if (!isset($_POST['showAll'])) {
            $this->Util()->DB()->setQuery($sql);
            $total = $this->Util()->DB()->GetSingle();
            $pages = $this->Util->HandleMultipages($this->page, $total, WEB_ROOT . "/resource-office");
            $sql_add = "LIMIT " . $pages["start"] . ", " . $pages["items_per_page"];
        }
        $sql = "SELECT a.* FROM office_resource a 
                 LEFT JOIN (SELECT c.office_resource_id,d.name AS nombre,c.status FROM responsables_resource_office c INNER JOIN personal d ON c.personalId=d.personalId WHERE c.status ='Activo') b ON a.office_resource_id = b.office_resource_id
                 WHERE 1 $filtro GROUP BY a.office_resource_id  ORDER BY a.office_resource_id DESC " . $sql_add;
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        /*foreach ($result as $key => $var) {
            $result[$key]["responsables"] = $this->getListResponsablesResource($var["office_resource_id"]);
            $this->setId($var['office_resource_id']);
            $result[$key]["upkeeps"] = $this->enumerateUpKeeps(true);
        }*/

        $data["items"] = $result;
        $data["pages"] = $pages;

        return $data;
    }

    private function generateDataReport()
    {
        global $personal;
        $typeDispositivos = ['Monitor', 'Hdmi', 'Cable VGA', 'Mouse', 'Teclado',  'Mousepad', 'Ventilador','Adicionales'];
        $typeSoftwares = ['Aspel COI', 'Aspel NOI', 'Aspel Facture', 'Admin XML', 'Office'. 'Licencia de Windows'];
        $data = $this->searchResource();
        $new = [];
        $puestos = $personal->getPuestos();
        foreach ($data['items'] as $key => $var) {
            $devices = $var['tipo_recurso'] === 'Computadora'
                ? $this->getDeviceResource($var['no_inventario'])
                : [];

            $softwares = $var['tipo_recurso'] === 'Computadora'
                ? $this->getSoftwareResource($var['no_inventario'])
                : [];

            $devices = array_column($devices, 'tipo_dispositivo');
            $softwares = array_column($softwares, 'tipo_software');
            $cad = $var;
            $cad["responsables"] = $this->getListResponsablesResource($var["office_resource_id"], true);
            if (count($cad["responsables"] )> 0) {
                $jefes = [];
                $personal->setPersonalId($cad['responsables'][0]['personalId']);
                $info = $personal->InfoWhitRol();
                $needle = strtolower(trim($info["nameLevel"]));
                $personal->deepJefesArray($jefes,true);

                foreach($puestos as $puesto) {
                   $cad['jefes'][$puesto['name']] = $jefes[$puesto['name']] ?? '';
                }
                if($needle === 'coordinador') {
                    $cad['jefes'][$needle] =  $jefes['me'];
                }
            }
            foreach ($typeDispositivos as $val)
                $cad[$val] = in_array($val, $devices);

            foreach ($typeSoftwares as $val2)
                $cad[$val2] = in_array($val2, $softwares);

            $new[] = $cad;
        }
        return $new;
    }

    public function generateReport()
    {
        $typeDispositivos = ['Monitor', 'Hdmi', 'Cable VGA', 'Mouse', 'Teclado', 'Mousepad', 'Ventilador','Adicionales'];
        $typeSoftwares = ['Aspel COI', 'Aspel NOI', 'Aspel Facture', 'Admin XML', 'Licencia de Windows', 'Office'];
        $data = $this->generateDataReport();
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);

        $sheet->setTitle('Reporte inventario');

        $sheet->setCellValueByColumnAndRow(0, 1, "EMPLEADO")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . 1)->getFont()->setBold(true);
        $sheet->mergeCellsByColumnAndRow(0,1,4,1);

        $sheet->setCellValueByColumnAndRow(5, 1, "COMPUTADORA")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(5) . 1)->getFont()->setBold(true);
        $sheet->mergeCellsByColumnAndRow(5,1,19,1);

        $sheet->setCellValueByColumnAndRow(20, 1, "ACCESORIOS")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(20) . 1)->getFont()->setBold(true);
        $sheet->mergeCellsByColumnAndRow(20,1,27,1);

        $sheet->setCellValueByColumnAndRow(28, 1, "SISTEMAS")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(28) . 1)->getFont()->setBold(true);
        $sheet->mergeCellsByColumnAndRow(28,1,33,1);


        $sheet->setCellValueByColumnAndRow(34, 1, "CONTRASEÑAS")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(34) . 1)->getFont()->setBold(true);
        $sheet->mergeCellsByColumnAndRow(34,1,39,1);


        $sheet->setCellValueByColumnAndRow(40, 1, "OBSERVACIONES FINALES")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(40) . 1)->getFont()->setBold(true);

        $col = 0;
        $row=2;
        $sheet->setCellValueByColumnAndRow($col, $row, "NOMENCLATURA")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "NOMBRE RESPONSABLE")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "AREA")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "CORREO INDIVIDUAL")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "CORREO GRUPAL")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "NO FISICO")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "ESTATUS")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "UBICACION")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "TIPO EQUIPO")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "FECHA DE COMPRA")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "COSTO DE COMPRA")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "MARCA")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "MODELO")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "SERIE")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "PROCESADOR")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "MEMORIA RAM")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'VELOCIDAD MHZ')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'TIPO')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'CAPACIDAD DEL DISCO DURO')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, "TIPO DE ALMACENAMIENTO")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $col++;
        foreach ($typeDispositivos as $head) {
            $sheet->setCellValueByColumnAndRow($col, $row, strtoupper($head))
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
            $col++;
        }
        foreach ($typeSoftwares as $head2) {
            $sheet->setCellValueByColumnAndRow($col, $row, strtoupper($head2))
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
            $col++;
        }
        $sheet->setCellValueByColumnAndRow($col, $row, "USUARIO COMPUTADORA")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, "CONTRASEÑA COMPUTADORA")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, "USUARIO ASPEL")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, "CONTRASEÑA ASPEL")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, "USUARIO OFFICE")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, "CONTRASEÑA OFFICE")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, "OBSERVACIONES")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

        $row++;

        foreach($data as $kt => $var) {
            $col = 0;

            $stringNombre = $var['responsables'][0]['nombre'];
            $nombreExp = explode(' ', $var['responsables'][0]['nombre']);

            $nomenclatura =  substr($stringNombre, 0, strlen($nombreExp[0]));
            $nombre       =  substr($stringNombre, strlen($nombreExp[0]), strlen($stringNombre));
            $area =  $var['responsables'][0]['departamento'] ?? '';
            $correo =  $var['responsables'][0]['email'] ?? '';
            $mailGrupo =  $var['responsables'][0]['mailGrupo'] ?? '';

            $sheet->setCellValueByColumnAndRow($col, $row, $nomenclatura);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $nombre);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $area);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $correo);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $mailGrupo);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['no_inventario'] === '' ? 'En bodega' : $var['no_inventario']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$var['status'] && $var['status'] != 'null' ? $var['status'] : '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['ubicacion']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['tipo_equipo']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['fecha_compra']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['costo_compra']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['marca']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['modelo']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['no_serie']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['procesador']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['memoria_ram']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['velocidad_procesador']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['tipo_memoria_ram']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['disco_duro']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['tipo_disco_duro']);
            $col++;
            foreach ($typeDispositivos as $head2) {

                $sheet->setCellValueByColumnAndRow($col, $row, $var[$head2] ? 'Si' : 'No');
                $col++;
            }
            foreach ($typeSoftwares as $head3) {
                $sheet->setCellValueByColumnAndRow($col, $row, $var[$head3] ? 'Si' : 'No');
                $col++;
            }
            $sheet->setCellValueByColumnAndRow($col, $row,$var['responsables'][0]['userComputadora'] ?? '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['responsables'][0]['passwordComputadora'] ?? '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['responsables'][0]['userAspel'] ?? '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['responsables'][0]['passwordAspel'] ?? '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['responsables'][0]['userOffice'] ?? '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['responsables'][0]['passwordOffice'] ?? '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, '');
            $row++;
        }
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer= PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile= "reporte_inventario".$_SESSION["User"]["userId"].".xlsx";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        $this->nameReport = $nameFile;
    }

    public function makeDownResource()
    {
        $this->validateResponsables();
        if ($this->Util()->PrintErrors())
            return false;

        $sql = " UPDATE office_resource 
                 SET status ='Baja',
                     motivo_baja='" . $this->getMotivoBaja() . "',
                     usuario_baja='" . $_SESSION['User']['username'] . "',
                     fecha_ultima_modificacion = '" . date("Y-m-d H:i:s") . "', 
                     fecha_baja='" . date('Y-m-d H:i:s') . "' WHERE office_resource_id = '" . $this->getId() . "' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
        $this->Util()->setError(0, "complete", "Se ha realizado la baja del registro");
        $this->Util()->PrintErrors();

        return true;
    }

    function validateResponsables()
    {
        $result = $this->getListResponsablesResource($this->getId());
        if (count($result) > 0)
            $this->Util()->setError(0, "error", "El registro tiene responsables activos, es necesario eliminarlos para proceder");
    }

    public function enumerateUpKeeps($incluirBaja = false)
    {

        $filtro = "";
        if (!$incluirBaja)
            $filtro .= " and upkeep_status ='Activo' ";

        $sql = "select * from upkeeps_resource_office where office_resource_id = '" . $this->getId() . "' $filtro order by upkeep_date desc ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }

    public function infoUpkeep()
    {
        $sql = "select * from upkeeps_resource_office where upkeep_resource_office_id = '" . $this->getUpkeepId() . "'  ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetRow();
    }

    public function saveUpkeepResource()
    {
        if ($this->Util()->PrintErrors())
            return false;

        if ($this->getUpkeepId()) {
            $sql = "UPDATE upkeeps_resource_office
                            SET 
                            upkeep_responsable = '" . $this->getUpkeepResponsable() . "',
                            upkeep_date = '" . $this->getUpkeepDate() . "',
                            upkeep_description = '" . $this->getUpkeepDescription() . "',
                            upkeep_user_modification = '" . $_SESSION['User']['username'] . "',
                            upkeep_date_modification = '" . date("Y-m-d H:i:s") . "'
                            WHERE upkeep_resource_office_id = '" . $this->getUpkeepId() . "'
                                 
                        ";
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->UpdateData();
            $this->Util()->setError(0, "complete", "Se ha actualizado un registro");
        } else {
            $sql = "INSERT INTO upkeeps_resource_office(
                             office_resource_id,
                             upkeep_responsable,
                             upkeep_date,
                             upkeep_description,
                             upkeep_date_create,
                             upkeep_user_create
                             )values(
                              '" . $this->getId() . "',
                              '" . $this->getUpkeepResponsable() . "',
                              '" . $this->getUpkeepDate() . "',
                              '" . $this->getUpkeepDescription() . "',
                              '" . date("Y-m-d H:i:s") . "',
                              '" . $_SESSION['User']['username'] . "'       
                             )
                            ";
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->InsertData();
            $this->Util()->setError(0, "complete", "Se ha guardado un registro");
        }
        $this->Util()->PrintErrors();
        return true;
    }

    public function deleteUpkeep()
    {
        if ($this->Util()->PrintErrors())
            return false;

        $sql = " UPDATE upkeeps_resource_office 
                 SET upkeep_status ='Baja',
                     upkeep_user_baja='" . $_SESSION['User']['username'] . "',
                     upkeep_date_baja = '" . date("Y-m-d H:i:s") . "' 
                     WHERE upkeep_resource_office_id = '" . $this->getUpkeepId() . "' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
        $this->Util()->setError(0, "complete", "Se ha realizado la baja del registro");
        $this->Util()->PrintErrors();

        return true;
    }

    private function syncDeviceToKit($lastId)
    {
        if (count($_SESSION['device_resource']) <= 0 || $this->getTipoRecurso() !== 'Computadora')
            return false;

        foreach ($_SESSION['device_resource'] as $var) {
            if ($var['deleteAction']) {
                $idResource = $var['office_resource_id'];
                $set = $var['typeDelete'] === 'deleteFromStock'
                    ? " status = 'Baja', motivo_baja='Baja definitiva realizada desde equipo de computo', fecha_baja=now(), usuario_baja='" . $_SESSION['User']['username'] . "' "
                    : " no_inventario='' ";

                $sql = "update office_resource set " . $set . " where office_resource_id='" . $idResource . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();

               /* $sql = "delete from device_resource where id='" . $var['id'] . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();*/
            } else{
               /* $sql = "insert into device_resource(office_resource_id, device_id)
                    values('" . $lastId . "','" . $var['office_resource_id'] . "')";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->InsertData();*/

                $sql = "update office_resource set no_inventario='" . $this->getNoInventario() . "' where office_resource_id='" . $var['office_resource_id'] . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
            }
        }
    }

    private function syncSoftwareToResource($lastId)
    {
        if (count($_SESSION['software_resource']) <= 0 || $this->getTipoRecurso() !== 'Computadora')
            return false;

        foreach ($_SESSION['software_resource'] as $var) {
            if ($var['deleteAction']) {
                $idResource = $var['office_resource_id'];
                $set = $var['typeDelete'] === 'deleteSoftwareFromStock'
                    ? " status = 'Baja', motivo_baja='Baja realizada desde equipo de computo', fecha_baja=now(), usuario_baja='" . $_SESSION['User']['username'] . "' "
                    : " no_inventario='' ";

                $sql = "update office_resource set " . $set . " where office_resource_id='" . $idResource . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();

               /* $sql = "delete from software_resource where id='" . $var['id'] . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();*/
            } else {
               /* $sql = "insert into software_resource(office_resource_id, software_id)
                    values('" . $lastId . "','" . $var['office_resource_id'] . "')";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->InsertData();*/

                $sql = "update office_resource set no_inventario='" . $this->getNoInventario() . "' where office_resource_id='" . $var['office_resource_id'] . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
            }
        }
    }
    public function calculateVencimientoSoftware ( $data = []) {
        $fvencimiento =  $data['vencimiento'];
        $vreturn['vencido'] = null;
        $vreturn['fvencimiento'] = null;
        $vreturn['diasxvencer'] = null;
        if($fvencimiento === null)
            return $vreturn;


        $vencido = date('Y-m-d') > $data['vencimiento'] ? true : false;
        $vreturn['vencido'] = $vencido;

        $date1= new DateTime($fvencimiento);
        $date2= new DateTime(date('Y-m-d'));
        $diff = $date1->diff($date2);
        $vreturn['fvencimiento'] = $fvencimiento;
        $vreturn['diasxvencer'] = $diff->days;
        return $vreturn;
    }
}
