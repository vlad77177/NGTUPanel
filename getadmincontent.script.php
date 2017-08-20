<?php

	session_start();
	
	include('classes/dbworker.class.php');
	include('classes/htmlconstructor.class.php');
	include('classes/stringconverter.class.php');
	include('classes/parser.class.php');
	include('a.charset.php');
	
	if(isset($_SESSION['login']) and isset($_SESSION['password'])){
		
		$htmlc=new HTMLConstructor();
		$dbw=new DBWorker();
		$strc=new StringConverter();
		$parser=new Parser();
		
		$dbw->ConnectToPostgreSQL();
		$dbw->SetUserData($_SESSION['login'], $_SESSION['password']);
		
		$userdata=$dbw->GetUserData();
		
		switch($_REQUEST['action']){			
			case 1:{
				//Пользователи
				$admin_content_users='';
				
				$admin_content_users_labels=array(
					'ADMPUSERMAIN-USER-LIST'=>$htmlc->ConstructUserList($dbw->GetAllUsersData()),
				    'ADMPUSERMAIN-SELECT-ROLE'=>$htmlc->ConstructUserRolesForCreate($userdata, $dbw,$strc),
					'CURRENT-USER-INF'=>''
				 );
				 
				$admin_content_users=$parser->TemplateParse('adminpanelusermain.template.html', $admin_content_users_labels);
				
				echo $admin_content_users;
				
				break;
			}
			case 2:{
				//Группы
				$admin_content_groups='';
				
				$admin_content_groups_labels=array(
						'NAMES-RIGHTS'=>$htmlc->ConstructRightsForNewGroup($dbw->GetRightsNames()),
						'GROUP-LIST'=>$htmlc->ConstructGroupList($dbw->GetGroupNames())
				);
				
				$admin_content_groups=$parser->TemplateParse('adminpanelgroupsmain.template.html', $admin_content_groups_labels);
				
				echo $admin_content_groups;
				
				break;
			}
			case 3:{
				//Логи
				echo 0;
				break;
			}
			case 4:{
				//Права выбранной группы
				$data=$dbw->GetGroupData($_POST['name']);
				echo $htmlc->ConstructRightCurrentGroup($dbw->GetRightsNames(),
						$strc->GetArrayFromPostgreString($data[0]['read']),
						$strc->GetArrayFromPostgreString($data[0]['add']),
						$strc->GetArrayFromPostgreString($data[0]['delete']));
				break;
			}
			case 5:{
				//Информация о пользователе
				$dbw=new DBWorker();
				$dbw->ConnectToPostgreSQL();
				echo $htmlc->ConstructCurrentUser($_POST['login'],$dbw);
				break;
			}
		}
	}
	else
		echo false;
?>