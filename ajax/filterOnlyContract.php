<?php
if((int)$User["level"] == 1){
    //Si seleccionaron TODOS
    if($formValues['respCuenta'] == 0){
        $personal->setActive(1);
        $socios = $personal->ListSocios();
        $idPersons= array();
        foreach($socios as $res){
            array_push($idPersons,$res['personalId']);
            $personal->setPersonalId($res['personalId']);
            $subordinados = $personal->Subordinados();
            if(empty($subordinados))
                continue;

            $subsLine = $util->ConvertToLineal($subordinados,'personalId');
            $idPersons=array_merge($idPersons,$subsLine);
            unset($subsLine);
            unset($subordinados);
        }//foreac
        $idPersons = array_unique($idPersons);
        $formValues['respCuenta'] =  $idPersons;
        $contracts = $contractRep->findContracts($formValues, true);

    }else{
        $idPersons = array();
        $respCuenta = $formValues['respCuenta'];
        array_push($idPersons,$respCuenta);
        if($formValues['subordinados']){
            $personal->setPersonalId($respCuenta);
            $subordinados = $personal->Subordinados();
            if(!empty($subordinados)){
                $subsLine = $util->ConvertToLineal($subordinados,'personalId');
                $idPersons=array_merge($idPersons,$subsLine);
                unset($subsLine);
                unset($subordinados);
            }
        }

        $formValues['respCuenta'] = $idPersons;
        $contracts = $contractRep->findContracts($formValues, true);
    }

}else{
    $idPersons = array();
    $respCuenta = $formValues['respCuenta']?$formValues['respCuenta']:$User['userId'];
    array_push($idPersons,$respCuenta);
    if($formValues['subordinados']){
        $personal->setPersonalId($respCuenta);
        $subordinados = $personal->Subordinados();
        if(!empty($subordinados)){
            $subsLine = $util->ConvertToLineal($subordinados,'personalId');
            $idPersons=array_merge($idPersons,$subsLine);
            unset($subsLine);
            unset($subordinados);
        }
    }
    $formValues['respCuenta'] = $idPersons;
    $contracts = $contractRep->findContracts($formValues, true);
}
?>