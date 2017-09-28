<?php
/* На удаление группы */
	session_start();
	
	include('includes.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	
	$dbw->SetUserData();
	
	if($dbw->CheckUserLog() and $dbw->CheckLoggedUserAdmin()){
		$userdata=$dbw->GetUserData();
		if($userdata[0]['role_type']=='superadm' or $userdata[0]['role_type']=='adm'){
			$dbw->DeleteGroup($_POST['gname']);
			echo true;
		}
		else echo false;
	}
	else echo false;
?>