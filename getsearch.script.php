<?php
	session_start();
	
	include('classes/dbworker.class.php');
	include('classes/stringconverter.class.php');
	include('classes/htmlconstructor.class.php');
	include('classes/parser.class.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	$dbw->ConnectToPostgreNGTU();
	
	$htmlc=new HTMLConstructor();
	
	$dbw->SetUserData();
	
	if($dbw->CheckUserLog()){
		
		$data_pattern=$dbw->GetPaternByName($_GET['pname'],true);
		$result=$dbw->GetSomeDataFromTable($_GET['pname']);
		$content='';
		for($i=0;$i<count($result);$i++){
			$content=$content.$htmlc->ConstructResultBlock($result[$i], $data_pattern,$dbw);
		}
		echo $content;
	}
	else echo false;
?>