<?php
/* На удаление пользователя из группы */
session_start();

include('includes.php');

$dbw=new DBWorker();
$dbw->ConnectToPostgreSQL();

$dbw->SetUserData();

if($dbw->CheckUserLog() and $dbw->CheckLoggedUserAdmin()){
	$user=new User($dbw->GetUserData());
	if($user->GetUserRole()==1 or $user->GetUserRole()==2){
		$user=new User($dbw->GetUserData($_POST['username']));
		$dbw->DropUserGroup($_POST['gname'], $user);
		echo true;
	}
	else echo false;
}
else echo false;
?>