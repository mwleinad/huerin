<?php

class Prospect extends Main
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

    private $name;

    public function setName($value)
    {
        $this->Util()->ValidateRequireField($value, "Nombre");
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
        $this->Util()->ValidateMail($value, "Email de contacto");
        $this->email = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    private $is_referred;

    public function setIsReferred($value)
    {
        $this->is_referred = $value;
    }
    public function getIsReferred()
    {
        return $this->is_referred;
    }

    private $type_referred;

    public function setTypeReferred($value)
    {
        $this->Util()->ValidateRequireField($value, "Referido por");
        $this->type_referred = $value;
    }

    public function getTypeReferred()
    {
        return $this->type_referred;
    }

    private $partner_id;

    public function setPartner($value)
    {
        $this->Util()->ValidateRequireField($value, "Asociados comerciales");
        $this->partner_id = $value;
    }

    public function getPartner()
    {
        return $this->partner_id;
    }

    private $name_referrer;

    public function setNameReferrer($value)
    {
        $this->Util()->ValidateRequireField($value, "Referente");
        $this->name_referrer = $value;
    }

    public function getNameReferrer()
    {
        return $this->name_referrer;
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

    public function info()
    {
        $sQuery = "select * from prospect 
                   where id = '" . $this->id . "' ";
        $this->Util()->DBProspect()->setQuery($sQuery);
        $row = $this->Util()->DBProspect()->GetRow();
        if ($row) {
            $sql = "select * from customer where customerId = '".$row['customer_id']."' ";
            $this->Util()->DB()->setQuery($sql);
            $row['customer'] = $this->Util()->DB()->GetRow() ?? null;
        }
        return $row;
    }

    public function enumerate()
    {
        $this->Util()->DBProspect()->setQuery('SELECT COUNT(*) FROM prospect');
        $total = $this->Util()->DBProspect()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total, WEB_ROOT . "/prospect");

        $sql_add = "LIMIT " . $pages["start"] . ", " . $pages["items_per_page"];
        $sQuery = "select  * from prospect a 
                             where 1 order by created_at desc " . $sql_add;
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

        $type_referred = (int)$this->is_referred === 0 ? 'NULL' : "'".$this->type_referred."'";
        $partner_id = (int)$this->type_referred != 'partner' ? 'NULL' : "'".$this->partner_id."'";
        $name_referrer = (int)$this->type_referred === 'partner' ? 'NULL' : "'".$this->name_referrer."'";

        $sql = "INSERT INTO prospect(
                    name,
                    phone,
                    email,
                    comment,
                    customer_id,
                    is_referred,
                    type_referred,
                    partner_id,
                    name_referrer, 
                    created_at,
                    updated_at
                ) VALUES (
                    '" . $this->name . "', 
                    '" . $this->phone . "',
                    '" . $this->email . "',
                    '" . $this->observation . "',
                    '" . $_POST['customer_exists'] . "',
                    '" . $this->is_referred . "',
                    $type_referred,
                    $partner_id,
                    $name_referrer,
                    now(),
                    now() 
                 )";
        $this->Util()->DBProspect()->setQuery($sql);
        $this->Util()->DBProspect()->InsertData();

        $this->Util()->setError(0, "complete", "Registro guardado");
        $this->Util()->PrintErrors();
        return true;
    }

    public function update()
    {
        if ($this->Util()->PrintErrors())
            return false;
        $type_referred = "type_referred = " . ((int)$this->is_referred === 0 ? 'NULL' : "'".$this->type_referred."'") .",";
        $partner_id = "partner_id = " . ($this->type_referred != 'partner' || (int)$this->is_referred  === 0 ? 'NULL' : "'".$this->partner_id."'" ) .",";
        $name_referrer = "name_referrer = " . ($this->type_referred === 'partner' || (int)$this->is_referred  === 0 ? 'NULL' : "'".$this->name_referrer."'") .",";
        $sql = "UPDATE prospect set 
                    name = '".$this->name."',
                    phone = '".$this->phone."',
                    email = '".$this->email."',
                    comment = '".$this->observation."',
                    is_referred = '".$this->is_referred."',
                    ".$type_referred."
                    ".$partner_id."
                    ".$name_referrer."
                    customer_id = '".$_POST['customer_exists']."',
                    updated_at = now()
                    WHERE id = '".$this->id."' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $this->Util()->DBProspect()->InsertData();

        $this->Util()->setError(0, "complete", "Registro actualizado");
        $this->Util()->PrintErrors();
        return true;
    }
}
