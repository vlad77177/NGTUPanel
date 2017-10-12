<?php

	session_start();

	include('includes.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	$sw=new SecurityWorker();
	
	$dbw->SetUserData();

	if($dbw->CheckUserLog() and $dbw->CheckLoggedUserAdmin()){
		if(isset($_POST['name'])){	
			/*
			$right_list=$dbw->GetRights();
			
			$matrix=json_decode($_POST['rights']);
			*/
			$read_list=array();
			$add_list=array();
			$delete_list=array();
			/*
			for($i=0;$i<count($matrix);$i++){
				if($matrix[$i][0]==true)
					$read_list[count($read_list)]=$right_list[$i]['id'];
				if($matrix[$i][1]==true)
					$add_list[count($add_list)]=$right_list[$i]['id'];
				if($matrix[$i][2]==true)
					$delete_list[count($delete_list)]=$right_list[$i]['id'];
			}	
			*/
						
			$name=$sw->CheckInsertString($_POST['name']);
			
			if($name!=''){
			
				$res=$dbw->AddGroup(
					$name,
					StringConverter::ConvertToPostgreString($read_list),
					StringConverter::ConvertToPostgreString($add_list),
					StringConverter::ConvertToPostgreString($delete_list)
				);
				
				echo true;			
			}			
		}
	}

?>