<?php

class Catalogue extends main
{
    function ListFilesExtension(){
        $this->Util()->DB()->setQuery('select * from mime_types order by name ASC');
        return $this->Util()->DB()->GetResult();
    }

}