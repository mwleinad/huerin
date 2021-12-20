<?php 

class Clasificacion extends Main
{
	private $id;
	private $nombre;

	public function setId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->id = $value;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setNombre($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, 'Nombre');
		$this->nombre = $value;
	}

	public function getNombre()
	{
		return $this->nombre;
	}

	public function EnumerateAll()
	{
		$this->Util()->DB()->setQuery('SELECT * FROM tipo_clasificacion WHERE ISNULL(fecha_eliminado) ORDER BY nombre ASC');
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}

	public function Enumerate()
	{
		$this->Util()->DB()->setQuery('SELECT COUNT(*) FROM tipo_clasificacion WHERE isnull(fecha_eliminado)');
		$total = $this->Util()->DB()->GetSingle();

		$pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/clasificacion");

		$sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
		$this->Util()->DB()->setQuery('SELECT * FROM tipo_clasificacion WHERE ISNULL(fecha_eliminado) ORDER BY nombre ASC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();

		$data["items"] = $result;
		$data["pages"] = $pages;
		return $data;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM tipo_clasificacion WHERE id = '".$this->id."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function saveOrUpdate()
	{
		if($this->Util()->PrintErrors()) { return false; }

        $sql = "CALL sp_guardar_catalogo(";
        $sql .="'".$this->id."'";
        $sql .=",'".$this->nombre."'";
        $sql .=",'clasificacion', @idRegistro)";

		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function delete()
	{
        if($this->Util()->PrintErrors()) { return false; }

        $sql = "CALL sp_eliminar_registro_catalogo(";
        $sql .="'".$this->id."'";
        $sql .=",'clasificacion')";

        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->DeleteData();

		$this->Util()->setError(3, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

}

?>