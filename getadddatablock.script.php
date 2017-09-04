<?php
	session_start();
	
	include('classes/dbworker.class.php');
	include('classes/htmlconstructor.class.php');
	include('classes/stringconverter.class.php');
	include('classes/securityworker.class.php');
	
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