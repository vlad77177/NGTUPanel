<?php
	session_start();
	
	include('classes/dbworker.class.php');
	include('classes/stringconverter.class.php');
	include('classes/securityworker.class.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	
	$dbw->SetUserData();
	
	if($dbw->CheckUserLog()){
		$dbw->ConnectToPostgreNGTU();
		$userdata=$dbw->GetUserData();
		if(SecurityWorker::CheckTheRightOfAdd($dbw->GetUserData(), $dbw->GetPaternByName($_POST['tablename'],true), $dbw)==true){
			if(count($userdata)>0 and isset($_POST['tablename']) and isset($_POST['tdata'])){
				$data=json_decode($_POST['tdata']);
				for($i=0;$i<count($data);$i++){
					if(is_array($data[$i]->{'value'})){
						//если value - массив
						$array_ids=array();
						$link=$dbw->GetLinkByName($data[$i]->{'name'});
						for($j=0;$j<count($data[$i]->{'value'});$j++){
							//перебор данных
							$data_pattern=$dbw->GetPaternByName($data[$i]->{'name'},true);
							if($data[$i]->{'created'}==true){
								$res=$dbw->AddData(
									$data[$i]->{'name'},
									$data[$i]->{'value'}[$j],
									StringConverter::GetArrayFromPostgreString($data_pattern[0]['col_types']),
									'col',
									'add',
									$link[0]['col_created_data']
								);
								$now_added=pg_fetch_result($res, 0, 0);
								$array_ids[$j]=$now_added;
							}
							else{
								$array_ids[$j]=$data[$i]->{'value'}[$j];
							}
						}
						$data[$i]->{'name'}=$link[0]['col_created_data'];
						$data[$i]->{'value'}=StringConverter::ConvertToPostgreString($array_ids);
					}
				}
				$data_pattern=$dbw->GetPaternByName($_POST['tablename'],true);
				echo $dbw->AddData($_POST['tablename'], $data,StringConverter::GetArrayFromPostgreString($data_pattern[0]['col_types']));
			}
			else echo false;
		}
		else echo 'У вас нет прав на добавление записи!';
	}
	else echo false;
?>