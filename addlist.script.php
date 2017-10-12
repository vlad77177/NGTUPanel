<?php

session_start();

include('includes.php');

$dbw=new DBWorker();
$dbw->ConnectToPostgreSQL();
$sw=new SecurityWorker();

$dbw->SetUserData();

if($dbw->CheckUserLog() and $dbw->CheckLoggedUserAdmin()){
	if(isset($_POST['name']) and isset($_POST['table'])){
		$name=$sw->CheckInsertString($_POST['name']);
		
		if($name!=''){
			$res=$dbw->AddList($name, $_POST['table']);
			echo true;
		}		
	}
}

?>