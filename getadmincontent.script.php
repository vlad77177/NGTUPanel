<?php

	session_start();
	
	include('includes.php');
	
	if(isset($_SESSION['login']) and isset($_SESSION['password'])){
		
		$htmlc=new HTMLConstructor();
		$dbw=new DBWorker();
		$strc=new StringConverter();
		$parser=new Parser();
		
		$dbw->ConnectToPostgreSQL();
		$dbw->SetUserData($_SESSION['login'], $_SESSION['password']);
		
		$user=new User($dbw->GetUserData());
		
		switch($_REQUEST['action']){			
			case 1:{
				//Пользователи
				$admin_content_users='';
				
				$users=array();
				$data=$dbw->GetAllUsersData();
				
				for($i=0;$i<count($data);$i++){
					$users[$i]=new User($data[$i]);
				}
				
				$admin_content_users_labels=array(
					'ADMPUSERMAIN-USER-LIST'=>LKConstructor::ConstructUserList($users),
				    'ADMPUSERMAIN-SELECT-ROLE'=>LKConstructor::ConstructUserRolesForCreate($user, $dbw,$strc),
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
						//'NAMES-RIGHTS'=>$htmlc->ConstructRightsForNewGroup($dbw->GetRightsNames()),
						'GROUP-LIST'=>LKConstructor::ConstructGroupList($dbw->GetGroupNames())
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
				$group=new Group($_POST['name'],$dbw);
				echo LKConstructor::ConstructRightCurrentGroup($group);
				break;
			}
			case 5:{
				//Информация о пользователе
				$dbw=new DBWorker();
				$dbw->ConnectToPostgreSQL();
				echo LKConstructor::ConstructCurrentUser($_POST['login'],$dbw);
				break;
			}
			case 6:{
				//Профиль пользователя
				$user=new User($dbw->GetUserData());
				
				$userdata_labels=array(
						'LK-AVATAR'=>LKConstructor::ConstructUMWAvatar(),
						'LK-TABLE'=>LKConstructor::ConstructUserTable($user),
				);
				
				echo Parser::TemplateParse('profile.template.html', $userdata_labels);
				
				break;
			}
			case 7:{//подгрузка расширения прав special-access-block
				$group=new Group($_GET['groupname'],$dbw);
				$r=$group->CheckAccessByID($_GET['rightid'], 'read');
				$a=$group->CheckAccessByID($_GET['rightid'], 'add');
				$d=$group->CheckAccessByID($_GET['rightid'], 'delete');
				if($r==true and $a==false and $d==false){
					echo 'Разрешено чтение';
				}
				else if($r==true and $a==true and $d==false){
					echo 'Разрешено чтение и редактирование';
				}
				else if($r==true and $a==true and $d==true){
					echo 'Полный доступ';
				}
				else{
					echo 'Доступ запрещен';
				}
				break;
			}
			case 8:{//Списки
				$content='';
				
				$selector='';
				$lists_list='';
				
				$rights=$dbw->GetRights();
				
				$selector=$selector.'<select size="1" >';
				for($i=0;$i<count($rights);$i++){
					if($rights[$i]['create_groups']=='t')
						$selector=$selector.'<option data-table="'.$rights[$i]['table'].'">'.StringConverter::ConvertTo1251($rights[$i]['name']).'</option>';
				}
				$selector=$selector.'</select>';
				
				$lists=array();
				$res_list=$dbw->GetLists();
				for($i=0;$i<count($res_list);$i++){
					$lists[$i]=new DataList(new DataListResultBuilder($res_list[$i]));
				}
				for($i=0;$i<count($rights);$i++){
					$current_lists=array();
					for($j=0;$j<count($lists);$j++){
						if($rights[$i]['table']==$lists[$j]->GetTable())
							$current_lists[count($current_lists)]=$lists[$j];
					}
					if(count($current_lists)>0){
						$divs='';
						for($k=0;$k<count($current_lists);$k++){
							$divs=$divs.'<div>'.StringConverter::ConvertTo1251($current_lists[$k]->GetName()).'</div>';
						}
						$labels=array(
								'LABEL'=>StringConverter::ConvertTo1251($rights[$i]['name']),
								'CONTENT'=>$divs
						);
						$lists_list=$lists_list.$parser->TemplateParse('currentlistsblock.template.html', $labels);
					}
				}
				
				$labels=array(
						'ITEM-SELECTOR'=>$selector,
						'LISTS-CONTENT'=>$lists_list
				);
				$content=Parser::TemplateParse('lists.template.html', $labels);
				echo $content;
				break;
			}
		}
	}
	else
		echo false;
?>