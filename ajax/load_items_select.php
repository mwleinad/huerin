<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch ($_POST['type']) {
    case 'sector':
        $result =  $catalogue->ListSectores();
        echo json_encode($result);
    break;
    case 'subsector':
        $result =  $catalogue->ListSubsectores($_POST['id']);
        echo json_encode($result);
    break;
    case 'actividad_comercial':
        $result = $catalogue->ListActividadesComerciales($_POST['id']);
        echo json_encode($result);
    break;
    case 'contract':
        $result = $customer->GetRazonesSociales($_POST['id'], '', 0, 'Si');
        if($_POST['contractId'] && count($result) > 0) {
            $key = array_search($_POST['contractId'], array_column($result, 'contractId'));
            unset($result[$key]);
        }
        $data = [
          [
              'id' =>0,
              'text' => "Otros datos"
          ]
        ];
        foreach($result as $var) {
          $cad['id'] = $var['contractId'];
          $cad['text'] = $var['name'];
          array_push($data, $cad);
        }
        echo json_encode($data);
        break;
     case 'defaultContract':
         $contract->setContractId($_POST['id']);
         $row = $contract->Info();
         if($row){
             $data = [
                     'id' => $row['contractId'],
                     'text' => $row['name']
             ];
         } else {
             if((int)$_POST['id'] ===0) {
                 $data = [
                     'id' => 0,
                     'text' => 'Otros datos'
                 ];
             }

         }
         echo json_encode($data);
         break;

    case 'responsableSupervisor':
        $personal->setPersonalId($_POST['id']);
        $subordinados = $personal->GetCascadeSubordinates();
        $supervisores = [];
        foreach ($subordinados as $sub) {
            if ($sub['nivel'] == 4) {
                $cad['id'] =  $sub['personalId'];
                $cad['name'] = $sub['name'];
                array_push($supervisores, $cad);
            }
        }
        echo json_encode($supervisores);
    break;
    case 'regimen':
        $result = $catalogue->ListRegimen($_POST['tax_purpose']);
        $res = [];
        foreach ($result as $item) {
            $cad['id'] =  $item['id'];
            $cad['text'] = $item['name'];
            array_push($res, $cad);
        }
        echo json_encode($res);
        break;
    case 'defaultRegimen':
        $row = $catalogue->DefaultSelectedRegimen($_POST['id'], $_POST['tax_purpose']);
        if($row){
            $data = [
                'id' => $row['id'],
                'text' => $row['name']
            ];
        } else {
            if((int)$_POST['id'] ===0) {
                $data = [
                    'id' => 0,
                    'text' => 'Otros datos'
                ];
            }
        }
        echo json_encode($data);
        break;

}
