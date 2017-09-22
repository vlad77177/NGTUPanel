<?php
	session_start();
	
	include('includes.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	$htmlc=new HTMLConstructor();
	
	$dbw->SetUserData();
	
	if($dbw->CheckUserLog()){
		$userdata=$dbw->GetUserData();
		if(count($userdata)>0 and isset($_GET['patternname'])){
			echo $htmlc->ConstructAddDataBlock($_GET['patternname'], $dbw);
		}
		else echo false;
	}
	else echo false;
?>