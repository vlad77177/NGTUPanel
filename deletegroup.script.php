<?php
/* На удаление группы */
	session_start();
	
	include('includes.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	
	$dbw->SetUserData();
	
	if($dbw->CheckUserLog() and $dbw->CheckLoggedUserAdmin()){
		$user=new User($dbw->GetUserData());
		if($user->GetUserRole()==1 or $user->GetUserRole()==2){
			$dbw->DeleteGroup($_POST['gname']);
			echo true;
		}
		else echo false;
	}
	else echo false;
?>