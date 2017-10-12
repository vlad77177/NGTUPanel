<?php
	session_start();
	
	include('includes.php');
	
	$dbw=new DBWorker();
	
	if($dbw->CheckUserLog()){
		
		$dbw->ConnectToPostgreSQL();
		
		$roleid=$dbw->GetRoleData($_POST['role']);
		$dbw->SetUserData($_SESSION['login'], $_SESSION['password']);
		$userdata=$dbw->GetUserData();
		
		if($userdata['role_type']=='superadm' or $userdata['role_type']=='adm'){
		
			$data=array(
					'login'=>$_POST['login'],
					'pass'=>$_POST['password'],
					'name'=>$_POST['name'],
					'surname'=>$_POST['surname'],
					'user_role'=>$roleid['id'],
					'reg_date'=>date('Y-m-d'),
					'who_reg'=>$userdata['id'],
					'groups'=>'{}'
			);
			
			$res=$dbw->AddUser($data);
			
			echo true;
		
		}
	}
?>