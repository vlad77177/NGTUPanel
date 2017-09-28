<?php
	session_start();
	
	include('includes.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	
	if($dbw->CheckUserLog()){
		$userdata=$dbw->GetUserData($_SESSION['login']);
		if($userdata[0]['role_type']=='superadm' or $userdata[0]['role_type']=='adm'){
			
			switch($_REQUEST['action']){
				case 1:{
					$group_id=$dbw->GetGroupData($_POST['groupname']);
					echo $res=$dbw->AddGroupForUser($_POST['username'], $group_id[0]['id']);
					break;
				}
			}
		}
		else echo 1;
	}
	else echo 2;	
?>