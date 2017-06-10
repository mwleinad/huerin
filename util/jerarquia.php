<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");
	
	
/*	SELECT t1.name AS lev1, t2.name as lev2, t3.name as lev3, t4.name as lev4
FROM category AS t1
LEFT JOIN category AS t2 ON t2.parent = t1.category_id
LEFT JOIN category AS t3 ON t3.parent = t2.category_id
LEFT JOIN category AS t4 ON t4.parent = t3.category_id
WHERE t1.name = 'ELECTRONICS';*/
	
function getPath($inputId = 0, $idList=array())
{   
		$db = new DB();    
    $sql ="SELECT * FROM personal where personalId='".$inputId."'";
    $db->setQuery($sql);
    $result = $db->GetResult($sql);

    if($result){
        $currentId = $result[0]["personalId"];
        $parentId = $result[0]["jefeInmediato"];

        $idList[] = $currentId;

        if ($parentId !=0){
           return getPath($parentId, $idList);
        }
    }
    return $idList;
}

$sql ="SELECT personal.personalId, personal.name, personal.tipoPersonal, personal.jefeInmediato, jefes.name AS jefeName FROM personal
	LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato ORDER BY name ASC";
$db->setQuery($sql);
$result = $db->GetResult($sql);

$jerarquia = $personal->Jerarquia($result);
//print_r($tree);
?>
<table border=1>
<tr>
  <td>Tipo Personal</td>
  <td>Nombre</td>
  <td>Jefe Inmediato</td>
</tr>
<?php 
//$print = $personal->printTree($jerarquia);

$_SESSION["lineal"] = array();
$personal->JerarquiaLineal($jerarquia);

foreach($_SESSION["lineal"] as $key => $value)
{
	?>
			<tr>
				<td><?php echo $value["tipoPersonal"];?></td>
				<td><?php echo $value["name"];?></td>
				<td><?php echo $value["jefeName"];?></td>
			</tr>
	<?php 
}
//print_r($personal->JerarquiaLineal($jerarquia));
?>
</table>