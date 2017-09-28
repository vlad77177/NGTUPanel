<?php
/* —крипт на добавление записи */
	session_start();
	
	include('includes.php');
	
	$dbw=new DBWorker();
	$dbw->ConnectToPostgreSQL();
	
	$dbw->SetUserData();
	
	if($dbw->CheckUserLog()){
		$dbw->ConnectToPostgreNGTU();
		$userdata=$dbw->GetUserData();
		
		$item=new Item(new ItemJSONBuilder($_POST['tablename'],$_POST['tdata']));
		$pattern=new DataPattern($item->GetTable(), $dbw);	
		
		if(SecurityWorker::CheckTheRightOfAdd($dbw->GetUserData(),$pattern,$dbw)==true){
			if(count($userdata)>0 and isset($_POST['tablename']) and isset($_POST['tdata'])){
				for($i=0;$i<count($pattern->GetLinks());$i++){
					//$link=new Link;
					$link=$pattern->GetLink($i);
					if($link!=null and $pattern->GetColtypes($i)==5){
						$array_of_ids=array();
						$now=$item->GetValues($i);
						for($j=0;$j<count($now);$j++){
							//echo json_encode($now);
							$col=array();
							$val=array();
							for($k=0;$k<count($now[0]);$k++){
								$col[$k]=$now[$j][$k]->{'column'};
								$val[$k]=$now[$j][$k]->{'value'};
							}
							//echo json_encode($col).' '.json_encode($val);
							$res=$dbw->AddData(
								$link->GetTableName(),
								$col,
								$val,
								$pattern->GetColtypes(),
								$link->GetColCreatedData()
							);
							$res=pg_fetch_result($res, 0, 0);
							$array_of_ids[$j]=$res;
						}
						$item->SetValue($i, StringConverter::ConvertToPostgreString($array_of_ids));
					}// норм сделано, но сложновато...
				}
				
				$dbw->AddData(
					$item->GetTable(),
					$pattern->GetColnames(),
					$item->GetValues(),
					$pattern->GetColtypes()
				);
			}
			else echo false;
		}
		else echo '” вас нет прав на добавление записи!';
		
		/* на случай просерани€ полимеров
		if(SecurityWorker::CheckTheRightOfAdd($dbw->GetUserData(), $dbw->GetPaternByName($_POST['tablename'],true), $dbw)==true){
			if(count($userdata)>0 and isset($_POST['tablename']) and isset($_POST['tdata'])){
				$data=json_decode($_POST['tdata']);
				for($i=0;$i<count($data);$i++){
					if(is_array($data[$i]->{'value'})){
						//если value - массив
						$array_ids=array();
						$link=$dbw->GetLinkByName($data[$i]->{'column'});
						$data_pattern=$dbw->GetPaternByName($data[$i]->{'column'},true);
						for($j=0;$j<count($data[$i]->{'value'});$j++){
							//перебор данных
							if($data[$i]->{'create'}==true){
								//предварительное добавление данных
								$res=$dbw->AddData(
										$data[$i]->{'column'},
										$data[$i]->{'value'}[$j],
										StringConverter::GetArrayFromPostgreString($data_pattern[0]['col_types']),
										$link[0]['col_created_data']
										);
								$now_added=pg_fetch_result($res, 0, 0);
								//сбор добавленных id
								$array_ids[$j]=$now_added;
							}
							else{
								$array_ids[$j]=$data[$i]->{'value'}[$j];
							}
						}
						$data[$i]->{'column'}=$link[0]['col_created_data'];
						$data[$i]->{'value'}=StringConverter::ConvertToPostgreString($array_ids);
					}
				}
				$data_pattern=$dbw->GetPaternByName($_POST['tablename'],true);
				echo json_encode($data);
				echo $dbw->AddData($_POST['tablename'], $data,StringConverter::GetArrayFromPostgreString($data_pattern[0]['col_types']));
			}
			else echo false;
		}
		else echo '” вас нет прав на добавление записи!';*/
	}
	else echo false;
?>