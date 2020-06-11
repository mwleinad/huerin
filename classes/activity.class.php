<?php 
class Activity extends Main {
    private $id;
    private $name;
    private $subsector_id;

    /**
     * @param mixed $id
     */
    public function setId($id)  {
        $this->Util()->ValidateInteger($id);
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->Util()->ValidateRequireField($name, 'Nombre');
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $subsector_id
     */
    public function setSubsectorId($subsector_id) {
        $this->Util()->ValidateRequireField($subsector_id, 'Subsector');
        $this->Util()->ValidateInteger($subsector_id);
        $this->subsector_id = $subsector_id;
    }

    /**
     * @return mixed
     */
    public function getSubsectorId() {
        return $this->subsector_id;
    }

    public function info() {
        $query = "select a.*, b.sector_id
                  from actividad_comercial a
                  inner join subsector b on a.subsector_id = b.id
                  where a.id = '".$this->id."' ";
        $this->Util()->DB()->setQuery($query);
        return $this->Util()->DB()->GetRow();
    }

    public function Enumerate()
    {
        $query = "select count(*) from actividad_comercial a 
                  inner join subsector b on a.subsector_id = b.id 
                  inner join sector c on b.sector_id = c.id ";
        $this->Util()->DB()->setQuery($query);
        $total = $this->Util()->DB()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/activity");

        $sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
        $query = "select a.*, b.name as subsector, c.name as sector from actividad_comercial a 
                  inner join subsector b on a.subsector_id = b.id 
                  inner join sector c on b.sector_id = c.id order by a.name asc, b.name asc, c.name asc $sql_add";
        $this->Util()->DB()->setQuery($query);
        $result = $this->Util()->DB()->GetResult();
        $data["items"] = $result;
        $data["pages"] = $pages;
        return $data;
    }
    public function save() {
        if($this->Util()->PrintErrors())
            return false;

        $id = $this->id ? $this->id : null;
        $name = $this->name;
        $subsector_id = $this->subsector_id;

        $query = "replace into actividad_comercial
                    (
                      id,
                      name, 
                      subsector_id
                    ) values (
                       '$id',
                       '$name',
                       '$subsector_id'       
                    )";
       $this->Util()->DB()->setQuery($query);
       $this->Util()->DB()->InsertData();

       $this->Util()->setError(0, 'complete', 'Información registrada');
       $this->Util()->PrintErrors();

       return true;
    }

    public function delete() {
        if($this->Util()->PrintErrors())
            return false;

        $query = "delete from actividad_comercial where id = '".$this->id."' ";
        $this->Util()->DB()->setQuery($query);
        $this->Util()->DB()->DeleteData();
        $this->Util()->setError(0, 'complete', 'Información eliminada');
        $this->Util()->PrintErrors();
        return true;
    }

}
?>