<?php

class WorkTeam extends  Main {

    private $id;

    public function setId($value) {
       $this->id =  $value;
    }

    private $name;

    public  function setName( $value) {
        $this->Util()->ValidateRequireField($value, 'Nombre');
        $this->name = $value;
    }

    public function Info() {
        $sql = "select * from work_team where id = '".$this->id."' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        if ($row) {
            $sql = "select * from personal_work_team where work_team_id = '".$this->id."'";
            $this->Util()->DB()->setQuery($sql);
            $result= $this->Util()->DB()->GetResult();
            $current = [];
            foreach($result as $var)
                $current[$var['departament_id']] = $var['personal_id'];

            $row['current_responsable'] = $current;
        }
        return $row;
    }

    public function Enumerate() {
        $sql = "select * from work_team order by name asc";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }

    public function save () {

        if($this->Util()->PrintErrors())
            return false;

        $sql = "insert into work_team (name) values ('".$this->name."')";
        $this->Util()->DB()->setQuery($sql);
        $id = $this->Util()->DB()->InsertData();

        // insert responsables
        $sql = "select departamentoId from departamentos";
        $this->Util()->DB()->setQuery($sql);
        $departaments= $this->Util()->DB()->GetResult();
        $sql = "insert into personal_work_team (personal_id, work_team_id, departament_id)values";
        $substr = "";
        foreach($departaments as $var) {
            $departament_id = $var['departamentoId'];
            $id_person = $_POST['res_'.$var['departamentoId']];
            if ($id_person) {
                $substr .="('".$id_person."', '".$id."', '".$departament_id."'),";
            }
        }
        if($id && strlen($substr)) {
            $query = $sql.substr($substr, 0, -1);
            $this->Util()->DB()->setQuery($query);
            $this->Util()->DB()->InsertData();
        }
        $this->Util()->setError(0, 'complete', 'Se han guarado los datos correctamente.');
        $this->Util()->PrintErrors();
        return true;
    }

    public function update () {

        if($this->Util()->PrintErrors())
            return false;

        $sql = "update work_team set name ='".$this->name."' where id='".$this->id."'";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();

        $sql = "select departamentoId from departamentos";
        $this->Util()->DB()->setQuery($sql);
        $departaments= $this->Util()->DB()->GetResult();
        $sql = "insert into personal_work_team (personal_id, work_team_id, departament_id)values";
        $substr = "";
        foreach($departaments as $var) {
            $departament_id = $var['departamentoId'];
            $id_person = $_POST['res_'.$var['departamentoId']];
            if ($id_person) {
                $substr .="('".$id_person."', '".$this->id."', '".$departament_id."'),";
            }
        }
        if($this->id && strlen($substr)) {
            $sqlDel = "delete from personal_work_team where work_team_id='".$this->id."'";
            $this->Util()->DB()->setQuery($sqlDel);
            $this->Util()->DB()->DeleteData();
            $query = $sql.substr($substr, 0, -1);
            $this->Util()->DB()->setQuery($query);
            $this->Util()->DB()->InsertData();
        }
        $this->Util()->setError(0, 'complete', 'Se han actualizado los datos correctamente.');
        $this->Util()->PrintErrors();
        return true;
    }

    public function delete () {

        if($this->Util()->PrintErrors())
            return false;

        $sql = "delete  from work_team where id='".$this->id."' ";
        $this->Util()->DB()->setQuery($sql);
        $affected = $this->Util()->DB()->DeleteData();

        if($affected) {
            $sqlDel = "delete from personal_work_team where work_team_id='".$this->id."'";
            $this->Util()->DB()->setQuery($sqlDel);
            $this->Util()->DB()->DeleteData();
        }
        $this->Util()->setError(0, 'complete', 'El registro se ha eliminado correctamente.');
        $this->Util()->PrintErrors();
        return true;
    }

    public function getAllByPersonalId ($id) {
        $sql = "select * from personal_work_team where work_team_id 
                in (select work_team_id from personal_work_team where personal_id = '".$id."')";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }


}
