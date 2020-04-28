<?php

class Catalogue extends Main
{
    function ListFilesExtension(){
        $this->Util()->DB()->setQuery('select * from mime_types order by name ASC');
        return $this->Util()->DB()->GetResult();
    }
    function EnumerateCatalogue($table = ""){
        if($table === "")
            return [];

        $sql  = "select * from $table where 1 ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
    function EnumerateFromArrayLineal($source = []) {
        $new_source = [];
        foreach($source as $src) {
            $tmp['name'] = $src;
            array_push($new_source, $tmp);
        }
        return $new_source;
    }

}