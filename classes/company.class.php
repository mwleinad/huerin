<?php

class Company extends Main
{
    private $id;

    public function setId($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    private $prospect_id;

    public function setProspectId($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->prospect_id = $value;
    }

    public function getProspectId()
    {
        return $this->prospect_id;
    }

    private $tax_purpose;

    public function setTaxPurpose($value)
    {
        $this->Util()->ValidateRequireField($value, "Tipo de persona");
        $this->tax_purpose = $value;
    }

    public function getTaxPurpose()
    {
        return $this->tax_purpose;
    }

    private $name;

    public function setName($value)
    {
        $this->Util()->ValidateRequireField($value, "Nombre o razon social");
        $this->name = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    private $phone;

    public function setPhone($value)
    {
        $this->Util()->ValidateRequireField($value, "Telefono");
        $this->phone = $value;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    private $email;

    public function setEmail($value)
    {
        $this->Util()->ValidateRequireField($value, "Email");
        $this->Util()->ValidateMail($value, "Email");
        $this->email = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    private $observation;

    public function setObservation($value)
    {
        $this->observation = $value;
    }

    public function getObservation()
    {
        return $this->observation;
    }

    private $legal_representative;

    public function setLegalRepresentative($value)
    {
        $this->legal_representative = $value;
    }

    public function getLegalRepresentative()
    {
        return $this->legal_representative;
    }

    private $business_activity;

    public function setBusinessActivity($value)
    {
        $this->Util()->ValidateRequireField($value, "Giro o actividad principal");
        $this->business_activity = $value;
    }

    public function getBusinessActivity()
    {
        return $this->business_activity;
    }

    private $regimen_id;

    public function setRegimenId($value)
    {
        $this->Util()->ValidateRequireField($value, "Régimen de contribución");
        $this->regimen_id = $value;
    }

    public function getRegimenId()
    {
        return $this->regimen_id;
    }

    private $constitution_date;

    public function setConstitutionDate($value)
    {
        if ($this->Util()->ValidateRequireField($value,
            $this->tax_purpose === 'moral' ? "Fecha de constitución" : 'Fecha de alta en el SAT'))
            if ($this->Util()->validateDateFormat($value, 'Fecha de constitución', 'd-m-Y'))
                $this->constitution_date = $this->Util()->FormatDateMySql($value);

    }

    public function getConstitutionDate()
    {
        return $this->constitution_date;
    }

    private $is_new_company;

    public function setIsNewCompany($value)
    {
        $this->is_new_company = $value;
    }

    public function isNewCompany()
    {
        return $this->is_new_company;
    }

    private $rfc;

    public function setRfc($value)
    {
        $this->rfc = $value;
    }

    public function getRfc()
    {
        return $this->rfc;
    }

    private  $arrayService = [];

    public function validateArrayService () {
        if(!isset($_POST['quotes']) || !count($_POST['quotes']))
            $this->Util()->setError(0, 'error', 'No existen servicios seleccionados');

        foreach($_POST['quotes'] as $quote) {
            if(!$this->Util()->isValidateDate($_POST['date_init_operation_'.$quote], 'd-m-Y')) {
                $this->Util()->setError(0, 'error', 'Falta fecha de inicio de operacion en uno de los servicios.');
                break;
            }

            if((int)$_POST['do_invoice_'.$quote] === 1) {
                if(!$this->Util()->isValidateDate($_POST['date_init_invoice_'.$quote], 'd-m-Y')) {
                    $this->Util()->setError(0, 'error', 'Falta fecha de inicio de facturacion en uno de los servicios que requieren factura.');
                    break;
                }
                if(!(int)$_POST['price_'.$quote]) {
                    $this->Util()->setError(0, 'error', 'El costo cotizado debe ser mayor a 0 cuando se requiere factura.');
                    break;
                }
            }
            $cad['service_id'] = $_POST['service_id_'.$quote];
            $cad['start_operation'] = $this->Util()->FormatDateMySql($_POST['date_init_operation_'.$quote]);
            $cad['start_invoice'] = (int)$_POST['do_invoice_'.$quote] === 1
                                    ? $this->Util()->FormatDateMySql($_POST['date_init_operation_'.$quote])
                                    : null;
            $cad['price'] = $_POST['price_'.$quote];
            $cad['name'] = $_POST['name_'.$quote];
            array_push($this->arrayService, $cad);
        }
    }

    public function info($withQuoteComplete = false)
    {
        $sQuery = "select * from company 
                   where id = '" . $this->id . "' ";
        $this->Util()->DBProspect()->setQuery($sQuery);
        $row = $this->Util()->DBProspect()->GetRow();

        if ($row) {
            $row['services'] = $this->serviceByCompany($withQuoteComplete);
            $sql = "select * from contract where contractId = '".$row['contract_id']."' ";
            $this->Util()->DB()->setQuery($sql);
            $row['contract'] = $this->Util()->DB()->GetRow() ?? null;
        }
        return $row;
    }

    public function enumerate()
    {
        $sql = "SELECT COUNT(*) FROM company
                 WHERE deleted_at is null and prospect_id = '" . $this->prospect_id . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $total = $this->Util()->DBProspect()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total, WEB_ROOT . "/company");

        $sql_add = "LIMIT " . $pages["start"] . ", " . $pages["items_per_page"];
        $sQuery = "select  * from company a 
                             where deleted_at is null
                             and prospect_id = '" . $this->prospect_id . "' order by created_at desc  " . $sql_add;
        $this->Util()->DBProspect()->setQuery($sQuery);
        $result = $this->Util()->DBProspect()->GetResult();
        $data["items"] = $result;
        $data["pages"] = $pages;
        return $data;
    }

    public function save()
    {
        if ($this->Util()->PrintErrors())
            return false;

        $sql = "INSERT INTO company(
                    prospect_id,
                    tax_purpose,
                    name,
                    taxpayer_id,
                    legal_representative,
                    activity_id,
                    regimen_id,
                    step_id,
                    date_constitution,
                    is_new_company,
                    comment,
                    contract_id,
                    created_at,
                    updated_at
                ) VALUES (
                    '" . $this->prospect_id . "',
                    '" . $this->tax_purpose . "',
                    '" . $this->name . "', 
                    '" . $this->rfc . "',
                    '" . $this->legal_representative . "',
                    '" . $this->business_activity . "',
                    '" . $this->regimen_id . "',
                    1,
                    '" . $this->constitution_date . "',
                    '" . $this->is_new_company . "',
                    '" . $this->observation . "',
                    '" . $_POST['contract_exists'] . "',
                    now(),
                    now()
                 )";
        $this->Util()->DBProspect()->setQuery($sql);
        $lastId = $this->Util()->DBProspect()->InsertData();

        $this->assocServiceToCompany($lastId, $_POST['services']);


        $sql = "insert into step_trace (company_id, step_name, made_by, comment, expiration_date,created_at)
                values( ?, ?, ?, ?, ?, ?)";
        $params = [];
        array_push($params, ['type' =>'i', 'value' => $this->$lastId]);
        array_push($params, ['type' =>'s', 'value' =>'En captura']);
        array_push($params, ['type' =>'s', 'value' => $_SESSION['User']['username']]);
        array_push($params, ['type' =>'s', 'value' => 'En captura']);
        $expiry_date = date("Y-m-d",strtotime(date('Y-m-d')."+ 1 days"));
        array_push($params, ['type' =>'s', 'value' => $expiry_date]);
        array_push($params, ['type' =>'s', 'value' => date('Y-m-d H:i:s')]);
        $this->Util()->DBProspect()->PrepareStmtQuery($sql, $params);
        $this->Util()->DBProspect()->InsertStmtData();

        $this->Util()->setError(0, "complete", "Registro guardado");
        $this->Util()->PrintErrors();
        return true;
    }

    private function serviceByCompany($quoteComplete = false)
    {
        $sql = "select * from company_service
                inner join service on company_service.service_id = service.id
                where company_service.company_id = '" . $this->id . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $result = $this->Util()->DBProspect()->GetResult();
        foreach ($result as $key => $val) {
            $result[$key]['quote_id'] = $this->getQuoteByService($val['company_id'], $val['service_id'], $quoteComplete);
        }
        return $result;
    }

    private function getQuoteByService($companyId, $serviceId, $quoteComplete = false)
    {
        $sql = "select * from quotation 
                where deleted_at is null and company_id = '" . $companyId . "' and service_id = '" . $serviceId . "'";
        $this->Util()->DBProspect()->setQuery($sql);
        $row = $this->Util()->DBProspect()->GetRow();
        return $quoteComplete ? $row : $row['id'];
    }

    public function assocServiceToCompany($id, $data = [])
    {
        $this->setId($id);
        $currentServices = $this->serviceByCompany();
        $currentCompany = $this->info();
        $currentServicesLineal = count($currentServices) ? array_column($currentServices, 'service_id') : [];
        $diff1 = array_diff($data, $currentServicesLineal);
        $diff2 = array_diff($currentServicesLineal, $data);
        $changeService = count($diff1) === 0 && count($diff2) === 0 ? false : true;
        $count = 0;
        $sql = "insert into company_service(company_id, service_id, created_at, updated_at) values";
        $sqlProspect = "insert into prospect_service(prospect_id, service_id) values";
        $compQuery = "";
        $compProspect = "";
        foreach ($data as $val) {
            $compQuery .= "(" . $id . ", " . $val . ", now(), now()),";
            $compProspect .= "(" . $currentCompany['prospect_id'] . ", " . $val . "),";
            $count++;

        }
        if ($count > 0) {
            $this->removeServiceFromCompany($currentCompany);
            $compQuery = substr($compQuery, 0, -1);
            $compProspect = substr($compProspect, 0, -1);
            $sql = $sql . $compQuery;
            $this->Util()->DBProspect()->setQuery($sql);
            $this->Util()->DBProspect()->InsertData();

            $sqlProspect = $sqlProspect . $compProspect;
            $this->Util()->DBProspect()->setQuery($sqlProspect);
            $this->Util()->DBProspect()->InsertData();

        }
        if ($changeService) {
            $sql = "update company set step_id = 1 where id = '" . $id . "' ";
            $this->Util()->DBProspect()->setQuery($sql);
            $this->Util()->DBProspect()->UpdateData();
        }
    }

    private function removeServiceFromCompany($dataCompany)
    {
        $sql = "delete from company_service 
                    where company_id = '" . $dataCompany['id'] . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $this->Util()->DBProspect()->DeleteData();

        $sql = "delete from prospect_service 
                    where prospect_id = '" . $dataCompany['prospect_id'] . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $this->Util()->DBProspect()->DeleteData();

    }

    public function update()
    {
        if ($this->Util()->PrintErrors())
            return false;
        $activity = 'activity_id = ' . ($this->business_activity ? $this->business_activity : 'NULL') . ',';
        $regimen = 'regimen_id = ' . ($this->regimen_id ? $this->regimen_id : 'NULL') . ',';
        $sql = "UPDATE company set 
                    tax_purpose = '" . $this->tax_purpose . "',
                    name = '" . $this->name . "',
                    taxpayer_id = '" . $this->rfc . "',
                    legal_representative = '" . $this->legal_representative . "',
                    " . $activity . "
                    " . $regimen . "
                    date_constitution = '" . $this->constitution_date . "',
                    is_new_company = '" . $this->is_new_company . "',
                    comment = '" . $this->observation . "',  
                    contract_id = '" . $_POST['contract_exists'] . "',                
                    updated_at = now()
                    WHERE id = '" . $this->id . "' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $this->Util()->DBProspect()->UpdateData();
        $this->assocServiceToCompany($this->id, $_POST['services']);
        $this->Util()->setError(0, "complete", "Registro actualizado");
        $this->Util()->PrintErrors();
        return true;
    }

    private function createOrIgnoreCustomerContract ($row) {
        global $customer, $contract, $log, $servicio;
        $dataCompany =  json_decode($row['data_company'], true);
        $dataProspect =  json_decode($row['data_prospect'], true);
        $dataRes = [];
        $this->validateArrayService();
        if ($this->Util()->PrintErrors())
            return false;

        $customer->setName($_POST['nameContact']);
        $customer->setPhone($_POST['phone']);
        $customer->setEmail($_POST['email']);
        $customer->setNameContact($_POST['nameContact']);
        $customer->setFechaAlta(date('Y-m-d'));
        if((int)$_POST['is_referred'] === 1) {
            $customer->setIsReferred($_POST['is_referred']);
            $customer->setTypeReferred($_POST['type_referred']);
            if($_POST['type_referred'] === 'partner')
                $customer->setPartner($_POST['partner_id']);

            if($_POST['type_referred'] === 'otro')
                $customer->setNameReferrer($_POST['name_referrer']);
        }
        $customer_id = (int)$dataProspect['customer_id'] ? $dataProspect['customer_id'] : $customer->Save(false);
        if ($customer->Util()->PrintErrors())
            return false;

        $contract->setCustomerId($customer_id);
        $contract->setType('Persona '.ucfirst($_POST['tax_purpose']));
        $contract->setFacturador($_POST['facturador']);
        $contract->setName($_POST['name']);
        $contract->setRfc($_POST['rfc']);
        $contract->setRegimenId($_POST['regimen_id']);

        if(isset($_POST['actividad_comercial']))
            $contract->setActividadComercialId($_POST['actividad_comercial']);

        $contract->setAddress($_POST['address']);
        $contract->setNoExtAddress($_POST['noExtAddress']);
        $contract->setNoIntAddress($_POST['noIntAddress']);
        $contract->setColoniaAddress($_POST['coloniaAddress']);
        $contract->setMunicipioAddress($_POST['municipioAddress']);
        $contract->setEstadoAddress($_POST['estadoAddress']);
        $contract->setPaisAddress($_POST['paisAddress']);
        $contract->setCpAddress($_POST['cpAddress']);
        $contract->setQualification('AAA');
        $contract->setNameRepresentanteLegal($_POST['legal_representative']);

        $contract->setDireccionComercial($_POST['direccionComercial']);
        $contract_id = (int)$dataCompany['contract_id'] ? $dataCompany['contract_id'] :  $contract->Save(false);

        if(!$contract_id && !$dataProspect['customer_id']) {
            $this->Util()->rollbackTable('customer', 'customerId', $customer_id);
        }

        if ($contract->Util()->PrintErrors())
            return false;

        $sql ="insert into servicio(contractId, tipoServicioId, costo, inicioOperaciones, inicioFactura) 
               VALUES (%d,%d,%f,%s,%s)";

        $current_services = $customer->GetServicesByContract($contract_id);
        $services_affected = [];
        foreach($this->arrayService as $serv) {
            $query = sprintf($sql, $contract_id, $serv['service_id'], $serv['price'],
                "'".$serv['start_operation']."'","'".$serv['start_invoice']."'");
            $this->Util()->DB()->setQuery($query);
            $lastId = $this->Util()->DB()->InsertData();
            $services_affected[] = $lastId;
            $log->saveHistoryChangesServicios($lastId, $serv['start_invoice'],'activo',
                                              $serv['price'],0, $serv['start_operation']);

            $servicio->setServicioId($lastId);
            $newServicio = $servicio->InfoLog();

            $log->setPersonalId($_SESSION['User']['userId']);
            $log->setFecha(date('Y-m-d H:i:s'));
            $log->setTabla('servicio');
            $log->setTablaId($lastId);
            $log->setAction('Insert');
            $log->setOldValue('');
            $log->setNewValue(serialize($newServicio));
            $log->SaveOnly();
        }
        if(count($services_affected))
            $log->sendLogMultipleOperation($services_affected, $contract_id,'new', $current_services);

        // en este punto es donde se debe enviar por correo. pendiente
        return true;
    }
    public function processSendToMain() {
        $params = [];
        array_push($params, ['type' =>'i', 'value' => $this->id]);
        $sql = "select json_object('type',company.tax_purpose, 'name', company.name,'rfc', company.taxpayer_id,
                'nameRepresentanteLegal', company.legal_representative, 'contract_id', company.contract_id) as data_company, 
                json_object('id', prospect.id, 'name', prospect.name,'phone', prospect.phone,'email', prospect.email,
                    'customer_id', prospect.customer_id) as data_prospect   
                from company
                inner join prospect on company.prospect_id = prospect.id
                where company.id = ? ";
        $this->Util()->DBProspect()->PrepareStmtQuery($sql, $params);
        $row = $this->Util()->DBProspect()->GetStmtRow();

        if(!$this->createOrIgnoreCustomerContract($row))
            return false;

        $params = [];
        array_push($params, ['type' =>'i', 'value' => $this->id]);
        $sql = "update company set step_id = 5 where id = ? ";
        $this->Util()->DBProspect()->PrepareStmtQuery($sql, $params);
        $this->Util()->DBProspect()->UpdateStmtData();

        $sql = "insert into step_trace (company_id, step_name, made_by, comment, expiration_date,created_at)
                values( ?, ?, ?, ?, ?, ?)";
        $params = [];
        array_push($params, ['type' =>'i', 'value' => $this->id]);
        array_push($params, ['type' =>'s', 'value' =>'Proceso de cotizacion finalizado']);
        array_push($params, ['type' =>'s', 'value' => $_SESSION['User']['username']]);
        array_push($params, ['type' =>'s', 'value' => 'El proceso de cotizacion ha finalizado, el prospecto fue un exito y ahora ya es parte de nuestra cartera de clientes.']);
        array_push($params, ['type' =>'i', 'value' => NULL]);
        array_push($params, ['type' =>'s', 'value' => date('Y-m-d H:i:s')]);
        $this->Util()->DBProspect()->PrepareStmtQuery($sql, $params);
        $this->Util()->DBProspect()->InsertStmtData();
        $this->Util()->setError(0,'complete', 'Proceso finalizado');
        $this->Util()->PrintErrors();
        return true;
    }

}
