<?php

class Question extends Main
{
    private $id;
    public function setId($value) {
        $this->id = $value;
    }
    public function info()
    {
        $sQuery = "select * from question 
                   where id = '" . $this->id . "' ";
        $this->Util()->DBProspect()->setQuery($sQuery);
        $row = $this->Util()->DBProspect()->GetRow();

        if ($row)
            $row['answer'] = $this->optionByQuestion($row['id']);

        return $row;
    }

    private function optionByQuestion($id) {
        $sql = "select * from answer
                where question_id = '" . $id . "' and deleted_at is null";
        $this->Util()->DBProspect()->setQuery($sql);
        $result =  $this->Util()->DBProspect()->GetResult();
        return $result;
    }
}
