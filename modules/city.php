<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('city');	
	/* End Session Control */
	
	session_start();
	
	$stateId = $_GET['stateId'];
	$_SESSION['idState'] = $stateId;
	
	$state->setStateId($stateId);
	$nomState = $state->GetNameById();
	
	$city->setStateId($stateId);
	$cities = $city->Enumerate();
		
	$smarty->assign("nomState", $nomState);
	$smarty->assign("cities", $cities);
	$smarty->assign('mainMnu','catalogos');

?>