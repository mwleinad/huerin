<?php


class Inventory extends Articulo
{
    private $nameReport;

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
        $sql = "INSERT INTO office_resource(
                            nombre,
                            descripcion,
                            tipo_recurso,
                            no_serie,
                            no_licencia,
                            codigo_activacion,
                            fecha_compra,
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
                              '" . $this->getFechaCompra() . "',
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
        $this->Util()->setError(0, "complete", "El registro se ha guardado correctamente.");
        $this->Util()->PrintErrors();
        return true;
    }

    public function updateResource()
    {
        if ($this->Util()->PrintErrors())
            return false;

        $sql = " UPDATE office_resource SET
                    nombre = '" . $this->getNombre() . "',
                    descripcion = '" . $this->getDescripcion() . "',
                    usuario_modificacion = '" . $_SESSION["User"]["username"] . "',
                    tipo_recurso =  '" . $this->getTipoRecurso() . "',
                    no_serie = '" . $this->getNoSerie() . "',
                    no_licencia = '" . $this->getNoLicencia() . "',
                    codigo_activacion = '" . $this->getCodigoActivacion() . "',
                    fecha_compra = '" . $this->getFechaCompra() . "',
                    tipo_equipo = '" . $this->getTipoEquipo() . "',
                    tipo_dispositivo = '" . $this->getTipoDispositivo() . "',
                    tipo_software = '" . $this->getTipoSoftware() . "',
                    marca = '" . $this->getMarca() . "',
                    modelo = '" . $this->getModelo() . "',
                    procesador = '" . $this->getProcesador() . "',
                    memoria_ram = '" . $this->getMemoriaRam() . "',
                    disco_duro = '" . $this->getDiscoDuro() . "',
                    no_inventario = '" . $this->getNoInventario() . "',
                    fecha_ultima_modificacion = '" . date("Y-m-d H:i:s") . "' 
                    WHERE office_resource_id = '" . $this->getId() . "'
                ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
        $this->syncDeviceToKit($this->getId());
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

        $sql = "select a.*,b.name as nombre, b.email  from responsables_resource_office a
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
            $devices = $this->getDeviceResource($info['office_resource_id']);
            if ($devices) {
                $info["device_resource"] = $devices;
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

    private function getDeviceResource($id)
    {
        $sql = "select a.id,a.device_id, b.* from device_resource a
                inner join office_resource b on a.device_id = b.office_resource_id
                where a.office_resource_id='" . $id . "' ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }

    public function enumerateResource()
    {
        /*$this->Util()->DB()->setQuery("SELECT COUNT(*) FROM office_resource WHERE status='Activo'");

        $total = $this->Util()->DB()->GetSingle();
        $pages = $this->Util->HandleMultipages($this->page, $total, WEB_ROOT . "/resource-office");
        $sql_add = "LIMIT " . $pages["start"] . ", " . $pages["items_per_page"];*/

        $this->Util()->DB()->setQuery('SELECT * FROM office_resource WHERE status="Activo" ORDER BY office_resource_id DESC ' . $sql_add);
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
        $this->Util()->DB()->setQuery("SELECT office_resource_id, nombre, tipo_equipo FROM office_resource WHERE status='Activo' ORDER BY nombre ASC ");
        return $this->Util()->DB()->GetResult();
    }

    public function listResourceInStock()
    {
        $this->Util()->DB()->setQuery("SELECT office_resource_id, marca, modelo,no_serie, tipo_dispositivo 
                                              FROM office_resource 
                                              WHERE status='Activo' and tipo_recurso= 'dispositivo'  and no_inventario=''
                                              ORDER BY office_resource_id ASC ");
        return $this->Util()->DB()->GetResult();
    }

    public function searchResource()
    {
        $filtro = "";

        $like = $_POST['name_descripcion'];
        $responsable = $_POST['responsable'];
        $tipoRecurso = $_POST['tipo_recurso'];
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
                 WHERE a.status='Activo' $filtro";

        if (!isset($_POST['showAll'])) {
            $this->Util()->DB()->setQuery($sql);
            $total = $this->Util()->DB()->GetSingle();
            $pages = $this->Util->HandleMultipages($this->page, $total, WEB_ROOT . "/resource-office");
            $sql_add = "LIMIT " . $pages["start"] . ", " . $pages["items_per_page"];
        }
        $sql = "SELECT a.* FROM office_resource a 
                 LEFT JOIN (SELECT c.office_resource_id,d.name AS nombre,c.status FROM responsables_resource_office c INNER JOIN personal d ON c.personalId=d.personalId WHERE c.status ='Activo') b ON a.office_resource_id = b.office_resource_id
                 WHERE a.status = 'Activo' $filtro GROUP BY a.office_resource_id  ORDER BY a.office_resource_id DESC " . $sql_add;
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
        $typeDispositivos = ['ventilador', 'cable_ventilador', 'hubusb', 'monitor', 'mouse', 'mousepad', 'ethernet', 'teclado',
            'nobreak', 'hdmi','convertidor_hdmi', 'convertidor_vga'];
        $data = $this->searchResource();
        $new = [];
        foreach ($data['items'] as $key => $var) {
            $devices = $var['tipo_recurso'] === 'equipo_computo'
                ? $this->getDeviceResource($var['office_resource_id'])
                : [];

            $devices = array_column($devices, 'tipo_dispositivo');
            $cad = $var;
            $cad["responsables"] = $this->getListResponsablesResource($var["office_resource_id"]);
            foreach ($typeDispositivos as $val)
                $cad[$val] = in_array($val, $devices) ? true : false;

            $new[] = $cad;
        }
        return $new;
    }

    public function generateReport()
    {
        $typeDispositivos = ['ventilador', 'cable_ventilador', 'hubusb', 'monitor', 'mouse', 'mousepad', 'ethernet', 'teclado',
            'nobreak', 'hdmi','convertidor_hdmi', 'convertidor_vga'];
        $data = $this->generateDataReport();
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $row = 1;
        $col = 0;
        $sheet->setTitle('Reporte inventario');
        $sheet->setCellValueByColumnAndRow($col, $row, "NO FISICO")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(++$col, $row, "TIPO EQUIPO")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(++$col, $row, "NOMBRE RESPONSABLE")
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
        $sheet->setCellValueByColumnAndRow(++$col, $row, "DISCO DURO")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(++$col, $row, "CORREO")
        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
        $col++;
        foreach ($typeDispositivos as $head) {
            $sheet->setCellValueByColumnAndRow($col, $row, strtolower(strtoupper($head)))
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
            $col++;
        }
        $row++;

        foreach($data as $kt => $var) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['no_inventario'] === '' ? 'En bodega' : $var['no_inventario']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, ucfirst($var['tipo_equipo']));
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['responsables'][0]['nombre']);
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
            $sheet->setCellValueByColumnAndRow($col, $row, $var['disco_duro']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $var['responsables'][0]['email']);
            $col++;
            foreach ($typeDispositivos as $head2) {
                $sheet->setCellValueByColumnAndRow($col, $row, $var[$head2] ? 'Si' : 'No');
                $col++;
            }
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
        if (count($_SESSION['device_resource']) <= 0 || $this->getTipoRecurso() !== 'equipo_computo')
            return false;

        foreach ($_SESSION['device_resource'] as $var) {
            if ($var['deleteAction']) {
                $idResource = $var['device_id'];
                $set = $var['typeDelete'] === 'deleteFromStock'
                    ? " status = 'Baja', motivo_baja='Baja realizada desde equipo de computo', fecha_baja=now(), usuario_baja='" . $_SESSION['User']['username'] . "' "
                    : " no_inventario='' ";

                $sql = "update office_resource set " . $set . " where office_resource_id='" . $idResource . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();

                $sql = "delete from device_resource where id='" . $var['id'] . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
            } elseif (!$var['id']) {
                $sql = "insert into device_resource(office_resource_id, device_id)
                    values('" . $lastId . "','" . $var['office_resource_id'] . "')";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->InsertData();

                $sql = "update office_resource set no_inventario='" . $this->getNoInventario() . "' where office_resource_id='" . $var['office_resource_id'] . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
            }
        }
    }
}
