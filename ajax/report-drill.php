<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();
switch($_POST["type"]) {
    case "levelOne":

        $year = $_POST['year'];
        $formValues['subordinados'] = $_POST['deep'];
        $formValues['respCuenta'] = $_POST['responsableCuenta'];
        $formValues['departamentoId'] = $_POST["departamentoId"];
        $formValues['cliente'] = $_POST["rfc"];
        $formValues['atrasados'] = $_POST["atrasados"];
        $formValues['year'] = $year;
        $formValues['activos'] = true;
        $formValues['sinServicios'] = false;//si se pasa departamentoId se debe evaluar que si el contrato esta sin servicio no debe salir

        //Actualizamos la clase del workflow, porque al generar los workflows la clase esta vacia (campo Class)
        $sql = "UPDATE instanciaServicio SET class = 'PorIniciar' WHERE class = ''";
        $db->setQuery($sql);
        $db->UpdateData();
        $contracts = array();
        include_once(DOC_ROOT.'/ajax/filterOnlyContract.php');
        $idClientes = array();
        $idContracts = array();
        $contratosClte = array();
        $nameRazones = array();
        foreach($contracts as $res){
            $contractId = $res['contractId'];
            $customerId = $res['customerId'];
            $nameRazon = $res['name'];

            if(!in_array($customerId,$idClientes))
                $idClientes[] = $customerId;

            if(!in_array($contractId,$idContracts)){
                $idContracts[] = $contractId;
                $contratosClte[$customerId][] = $res;
            }
        }//foreach
        $clientes = array();
        foreach($idClientes as $customerId){

            $customer->setCustomerId($customerId);
            $infC = $customer->Info();
            $infC['contracts'] = $contratosClte[$customerId];
            $clientes[] = $infC;
        }//foreach
        $smarty->assign("clientes", $clientes);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT.'/templates/lists/level-one.tpl');
    break;
    case 'findInstancias':
        $isParcial =  false;
        if($_POST['status']=="bajaParcial")
            $isParcial = true;
        $instancias = $instanciaServicio->getInstanciaByServicio($_POST['servicioId'],$_POST['year'],$_POST['initOp'],$isParcial);
        $smarty->assign("instancias", $instancias);
        $smarty->display(DOC_ROOT.'/templates/lists/instanciasDrill.tpl');
    break;
    case 'findPasos':
        $pasos = $workflow->listStepByWorkflow($_POST);
        $smarty->assign("pasos", $pasos);
        $smarty->display(DOC_ROOT.'/templates/lists/stepsDrill.tpl');
    break;

}