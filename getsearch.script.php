<?php
	session_start();
	
	include('includes.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	$dbw->ConnectToPostgreNGTU();
	
	$htmlc=new HTMLConstructor();
	
	$dbw->SetUserData();
	
	if($dbw->CheckUserLog()){
		
		$pattern=new DataPattern($_GET['pname'], $dbw);
		$content='';
		//$result=$dbw->GetSomeDataFromTable($_GET['pname']);
		$result=$dbw->GetNGTUTableRows($pattern->GetTableName(),null,null,null);
		for($i=0;$i<count($result);$i++){
			//$content=$content.$htmlc->ConstructResultBlock($result[$i], $data_pattern,$dbw);
			$content=$content.$htmlc->ConstructResultBlock(
				new Item(new ItemResultBuilder($result[$i], $pattern)),
				$pattern,
				$dbw
			);
		}
		echo $content;
		/* на случай просерания полимеров
		$data_pattern=$dbw->GetPaternByName($_GET['pname'],true);
		$result=$dbw->GetSomeDataFromTable($_GET['pname']);
		$content='';
		for($i=0;$i<count($result);$i++){
			$content=$content.$htmlc->ConstructResultBlock($result[$i], $data_pattern,$dbw);
		}
		echo $content;*/
	}
	else echo false;
?>