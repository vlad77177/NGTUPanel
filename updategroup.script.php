<?php
	session_start();
	
	include('classes/dbworker.class.php');
	include('classes/stringconverter.class.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	
	$dbw->SetUserData();
	
	if($dbw->CheckUserLog() and $dbw->CheckLoggedUserAdmin()){
		if(isset($_POST['gname']) and isset($_POST['nright'])){
			$userdata=$dbw->GetUserData();
			$right_list=$dbw->GetRightsNames();
			$matrix=json_decode($_POST['nright']);
			
			$read_list=array();
			$add_list=array();
			$delete_list=array();
			
			for($i=0;$i<count($matrix);$i++){
				if($matrix[$i][0]==true)
					$read_list[count($read_list)]=$right_list[$i]['id'];
				if($matrix[$i][1]==true)
					$add_list[count($add_list)]=$right_list[$i]['id'];
				if($matrix[$i][2]==true)
					$delete_list[count($delete_list)]=$right_list[$i]['id'];
			}
			
			$dbw->UpdateGroup(
					$_POST['gname'], 
					StringConverter::ConvertToPostgreString($read_list), 
					StringConverter::ConvertToPostgreString($add_list), 
					StringConverter::ConvertToPostgreString($delete_list)
			);
			
			echo true;
		}
		else echo false;
	}
	else echo false;
?>