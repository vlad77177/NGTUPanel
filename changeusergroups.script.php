<?php
	session_start();
	
	include('includes.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	
	if($dbw->CheckUserLog()){
		$user=new User($dbw->GetUserData($_SESSION['login']));
		if($user->GetUserRole()==1 or $user->GetUserRole()==2){	
			switch($_REQUEST['action']){
				case 1:{
					$group_id=$dbw->GetGroupData($_POST['groupname']);
					$dbw->AddGroupForUser($_POST['username'], $group_id['id']);
					break;
				}
			}
		}
		else echo 1;
	}
	else echo 2;	
?>