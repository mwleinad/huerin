<?php

    /* Star Session Control Modules*/
    $user->allowAccess(7);  //level 1
    $user->allowAccess(305);//level 2
    /* end Session Control Modules*/

	$smarty->assign('mainMnu','reportes');

	if($_SESSION["search"]["month"])
	{
		$month = $_SESSION["search"]["month"];
	}
	else
	{
		$month = date("m");
	}

	if($_SESSION["search"]["year"])
	{
		$year = $_SESSION["search"]["year"];
	}
	else
	{
		$year = date("Y");
	}
	
	$smarty->assign("month", $month);
	$smarty->assign("year", $year);
?>