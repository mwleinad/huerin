<?php
mysql_connect("127.0.0.1","root","root");
mysql_select_db("huerin");
include_once('init.php');
include_once('config.php');
include_once(DOC_ROOT.'/libraries.php');

?>
<a href="?z=listusers">Usuarios</a><br><br><hr>
<a href="?z=workflows">workflows</a><br><br><hr>
<?php
switch($_GET["z"])
{
	case "workflows":
		$workflow->setInstanciaServicioId("6113");
		$myWorkflow = $workflow->Info();
		echo "<pre>";
		print_r($myWorkflow);
		echo "</pre>";
	break;
	case "cxc":
		$values['facturador']="Efectivo";
		$cxc->SearchCuentasPorCobrar($values);
	//	$values['facturador']="15";
//		$cxc->SearchCuentasPorCobrar($values);
	break;
	case "listusers":
		$r=mysql_query("SELECT personalId,name,jefeSocio,jefeGerente,jefeContador,jefeSupervisor,tipoPersonal FROM personal order by tipoPersonal DESC");
		while($f=mysql_fetch_assoc($r))
		{
			$persona[]=$f;
		}
		getSub($persona);
	break;
	case "listclients":
			$r=mysql_query("SELECT DISTINCT * FROM customer
			LEFT JOIN contract ON customer.customerId=contract.customerId
			WHERE contract.responsableCuenta='".$_GET['id']."'");
			while($s=mysql_fetch_assoc($r))
			{
				echo $s['nameContact']."<br>";
			}
			echo "<hr>Subordinados<br>";
		$rr=mysql_query("SELECT DISTINCT personalId FROM personal WHERE jefeSocio='".$_GET['id']."' OR jefeGerente='".$_GET['id']."' OR jefeContador='".$_GET['id']."' OR jefeSupervisor='".$_GET['id']."'");
		while($x=mysql_fetch_assoc($rr))
		{
			$r=mysql_query("SELECT DISTINCT customer.*, tipoServicio.departamentoId AS departamento FROM customer, contract, servicio, tipoServicio
			WHERE contract.responsableCuenta='".$x['personalId']."' AND customer.customerId=contract.customerId
			AND contract.contractId=servicio.contractId AND servicio.tipoServicioId=tipoServicio.tipoServicioId");
			while($s=mysql_fetch_assoc($r))
			{
				echo "<i>Depto: </i><b>".$s['departamento']."</b> - ".$s['nameContact']."<br>";
			}
		}
		
	break;
}

//---------------------------------------------------------------------

function getSub(&$personal)
		{
		echo "<ul>";
			foreach($personal as $key1 => $value1)
			{
				echo "<li> id: <b>".$value1['personalId']."</b> <i>tipo: </i><b>".$value1['tipoPersonal']."</b> - <a href='?z=listclients&id=".$value1['personalId']."'>".$value1['name']."</a><ul>";
				foreach($personal as $key2 => $value2)
				{	
					if($value1['personalId'] == $value2['jefeSocio'] || $value1['personalId'] == $value2['jefeGerente'] || $value1['personalId'] == $value2['jefeContador'] || $value1['personalId'] == $value2['jefeSupervisor'])
					{
						echo "<li> id: <b>".$value2['personalId']."</b> <i>tipo: </i><b>".$value2['tipoPersonal']."</b> - <a href='?z=listclients&id=".$value2['personalId']."'>".$value2['name']."</a></li>";
					}
				}
				echo "</ul></li>";
			}
		echo "</ul>";
		}

?>