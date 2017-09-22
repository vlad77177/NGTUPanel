<?php
/* На удаление пользователя из группы */
session_start();

include('classes/dbworker.class.php');
include('classes/stringconverter.class.php');

$dbw=new DBWorker();
$dbw->ConnectToPostgreSQL();

$dbw->SetUserData();

if($dbw->CheckUserLog() and $dbw->CheckLoggedUserAdmin()){
	$userdata=$dbw->GetUserData();
	if($userdata[0]['role_type']=='superadm' or $userdata[0]['role_type']=='adm'){
		$user=$dbw->GetUserData($_POST['username']);
		$dbw->DropUserGroup($_POST['gname'], $user);
		echo true;
	}
	else echo false;
}
else echo false;
?>