<?php

include('includes.php');

if((isset($_POST['t_name']) and isset($_POST['p_number']) and isset($_POST['offset'])) or isset($_POST['inpage'])){
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreNGTU();
	$dbw->ConnectToPostgreSQL();
	$htmlc=new HTMLConstructor();
	
	switch($_REQUEST['action']){
		case 1:{			
			$tc=$dbw->GetTableColumns($dbw->GetNGTUConnection(),$_POST['t_name']);		
			echo TableConstructor::ConstructTable($tc, $dbw->GetTableData($_POST['t_name'],($_POST['offset']*($_POST['p_number']-1)), $_POST['offset'], $tc[0]['column_name']));
			break;
		}
		case 2:{		
			$rows_number=$dbw->GetNumberRowsOfTable($dbw->GetNGTUConnection(),$_POST['t_name']);
			echo TableConstructor::ConstructPages(floor($rows_number/$_POST['inpage'])+1);
			break;
		}
	}
}
else
	echo 'Ошибка!';

?>