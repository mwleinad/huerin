<?php

class Filtro extends Util
{
	public function AssignedPersonal($tipoPersonal)
	{
		global $infoUser;

		//Socio y Asistente pueden ver todo el personal.
		if ($tipoPersonal != "Socio" && $tipoPersonal != "Asistente") 
		{
			$sqlFilter = " 
			AND (personalId='".$infoUser['personalId']."'
			OR jefeSocio='".$infoUser['personalId']."'
			OR jefeGerente='".$infoUser['personalId']."'
			OR jefeContador='".$infoUser['personalId']."'
			OR jefeSupervisor='".$infoUser['personalId']."')";
    }

		$sqlActive = " AND active = '1'";

		$sql = "SELECT
					personalId
				FROM
					personal WHERE 1
				".$sqlFilter.$sqlActive."
				ORDER BY
					name ASC";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}
	
	public function WithPermission($roleId, $conPermiso, $subordinadosPermiso, &$result = false, $servicio = false, $key = 0, $keyContract = 0)
	{
		global $rol;
	    $withPermission = false;
        //comprobar el rol si es de tipo limitado pasando nombre de roles que queremos limitar(el gerente igual debe ser limitado)
        //hasta este apartado el rol cliente no debe tener problemas por que ya esta filtrado a que solo sus contratos pueda ver.
        $rol->setRolId($roleId);
        $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor','contador','auxiliar','asistente','sistema'),array('Juridico RRHH','Subgerente','socio'));
		//if el rol del usuario tiene privilegio de ver todos los contrartos
		if($unlimited){
			$withPermission = true;
			$result[$key]["contracts"][$keyContract]['instanciasServicio'][$servicio["servicioId"]] = $servicio;
		}
		else
		{
			foreach($subordinadosPermiso as $usuarioPermiso)
			{				
				if(in_array($usuarioPermiso, $conPermiso))
				{
					$withPermission = true;
					$result[$key]["contracts"][$keyContract]['instanciasServicio'][$servicio["servicioId"]] = $servicio;
					break;
				}
			}
		}//else
		return $withPermission;
	}
	
  public function UsuariosConPermiso($permisos, $extraId)
  {
		$permisos = explode("-", $permisos);

		foreach ($permisos as $permiso) {
			list($depa, $resp) = explode(",", $permiso);

			if($depa == 25) {
				continue;
			}

			if ($resp) {
				$misPermisos[$depa] = $resp;
			}
		}

		if (count($misPermisos) == 0) {
		  $misPermisos[1] = $extraId;
		}

		return $misPermisos;

  }
		
	function Subordinados($id, $addMe = false)
	{
		$personal = new Personal;
		$personal->setPersonalId($id);
		$subordinados = $personal->Subordinados();

		if($addMe === true)
		{
			array_push($subordinados, array("personalId" => $id));
		}

		return $subordinados;
	}
	
	function SubordinadosPermiso($type = 'propio', $subordinados, $userId)
	{
		if($type == "propio")
		{			
            $subordinadosPermiso = array($userId);
        }
		else
		{
			$subordinadosPermiso = array();
            foreach ($subordinados as $sub)
			{
       	       array_push($subordinadosPermiso, $sub["personalId"]);
            }
		     array_push($subordinadosPermiso, $userId);
         }//else
		
		return $subordinadosPermiso;
	}
	/*
	 * funcion showByDefault
	 * comprobar el rol si es de tipo ilimitado pasando pezados de nombre de roles que son limitados
	 * devuelve 1 si el rol es ilimitado o bien el userId de la session activa sea igual al id encargado de dar de alta a los clientes(supervisor juridico RHH)
	 */
	function ShowByDefault($servicios, $roleId)
	{
	    global $rol;
        $rol->setRolId($roleId);
        $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor','contador','auxiliar','asistente','sistema'),array('Juridico RRHH','Subgerente','socio'));
		if((count($servicios) == 0 && $unlimited)){
			return 1;
		}
		return 0;
	}
	
	function ShowByInstances($instancias, &$result, $key = 0, $keyContract = 0)
	{
		if (count($instancias) > 0)
		{
			$result[$key]["servicios"]++;
			//solo los contratos activos pueden ser tomados en cuenta para mostrar los botones de baja temporal
			if($result[$key]['contracts'][$keyContract]['activo']=='Si'){
                $result[$key]["doBajaTemporal"]=1;
            }
			return 1;
		}
		//unset($result[$key]["contracts"][$keyContract]);
		return 0;
	}
	
	function RemoveClientFromView($showCliente, $roleId, $type, &$result, $key = 0)
	{
	    global $rol;
        $rol->setRolId($roleId);
        $unlimited =$rol->ValidatePrivilegiosRol(array('gerente','supervisor','contador','auxiliar','asistente','sistema'),array('Juridico RRHH','socio'.'Gerente de administracion','Recepcion'));
		if (($showCliente === 0 && (!$unlimited)))
		{
			unset($result[$key]);
		}
	}
}
?>