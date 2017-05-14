<?php

//	$empresa->AuthUser();

	$info = $empresa->Info();
	$smarty->assign("info", $info);

	$smarty->assign('mainMnu','catalogos');
?>