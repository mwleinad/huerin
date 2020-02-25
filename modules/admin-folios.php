<?php
	switch($_GET['section']){
		case 'nuevos-folios':
		      $user->allowAccess(217);
		      $user->allowAccess(139);
              $id_rfc =  $_GET['id'];

              if(!$id_rfc){
                  header('Location: '.WEB_ROOT);
              }
              $rfc->setRfcId($id_rfc);
              $info = $rfc->InfoRfc();

			  $folios->setIdRfc($id_rfc);
			  $listFolios = $folios->GetFoliosByRfc();

			  $smarty->assign('folios', $listFolios);
              $smarty->assign('rfcInfo', $info);
            break;
        case 'emisores':
            $user->allowAccess(217);
            $user->allowAccess(140);
            $smarty->assign('results', $rfc->EnumerateRfc());
        break;

	}//switch
	$smarty->assign('mainMnu','configuracion');
?>