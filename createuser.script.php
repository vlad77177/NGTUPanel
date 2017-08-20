<?php
	session_start();
	
	include('classes/dbworker.class.php');
	include('classes/stringconverter.class.php');
	
	$dbw=new DBWorker();
	$strc=new StringConverter();
	
	if($dbw->CheckUserLog()){
		
		$dbw->ConnectToPostgreSQL();
		
		$roleid=$dbw->GetRoleID($_POST['role']);
		$dbw->SetUserData($_SESSION['login'], $_SESSION['password']);
		$userdata=$dbw->GetUserData();
		
		if($userdata[0]['role_type']=='superadm' or $userdata[0]['role_type']=='adm'){
		
			$data=array(
					'login'=>$_POST['login'],
					'pass'=>$_POST['password'],
					'name'=>$_POST['name'],
					'surname'=>$_POST['surname'],
					'user_role'=>$roleid[0]['id'],
					'reg_date'=>date('Y-m-d'),
					'who_reg'=>$userdata[0]['id'],
					'groups'=>'{}'
			);
			
			$res=$dbw->AddUser($data);
			
			echo true;
		
		}
	}
?>