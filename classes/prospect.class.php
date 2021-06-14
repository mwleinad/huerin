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
        return $this->Util()->DBProspect()->GetRow();
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

        $sql = "INSERT INTO prospect(
                    name,
                    phone,
                    email,
                    comment,
                    customer_id
                ) VALUES (
                    '" . $this->name . "', 
                    '" . $this->phone . "',
                    '" . $this->email . "',
                    '" . $this->observation . "',
                    '" . $_POST['customer_exists'] . "' 
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

        $sql = "UPDATE prospect set 
                    name = '".$this->name."',
                    phone = '".$this->phone."',
                    email = '".$this->email."',
                    comment = '".$this->observation."',
                    customer_id = '".$_POST['customer_exists']."'
                    WHERE id = '".$this->id."' ";
        $this->Util()->DBProspect()->setQuery($sql);
        $this->Util()->DBProspect()->InsertData();

        $this->Util()->setError(0, "complete", "Registro actualizado");
        $this->Util()->PrintErrors();
        return true;
    }
}
