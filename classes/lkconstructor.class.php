<?php
class LKConstructor{
	static function ConstructLK(DBWorker $dbw){
		
		$user=new User($dbw->GetUserData());
		
		$admin_content='';
		
		if($user->GetUserRole()==1 or $user->GetUserRole()==2){
			$admin_content=file_get_contents('adminpanel.template.html');
		}
		
		$userdata_labels=array(
				'LK-ADMINPANEL-CONTENT'=>$admin_content
		);
		
		return Parser::TemplateParse('lk.template.html', $userdata_labels);
	}
	static function ConstructGroupList($gn){
		if($gn==null)
			return 'Небыло создано еще ни одной группы!';
			else{
				$content='<table>';
				for($i=0;$i<count($gn);$i++){
					$content=$content.'<tr><td><div onclick="getrights('
							.$i.')" class="class-pointable">'
									.StringConverter::ConvertTo1251($gn[$i]['name']).'</div></td><td><img src="images/minus.png" onclick="deletegroup(\''
											.StringConverter::ConvertTo1251($gn[$i]['name']).'\')"></td></tr>';
				}
				$content=$content.'</table>';
				return $content;
			}
	}
	
	static function ConstructUserList($user){
		$content='';
		$content=$content.'<table>';
		for($i=0;$i<count($user);$i++){
			$content=$content.'<tr name="'.StringConverter::ConvertTo1251($user[$i]->GetLogin()).'" class="class-pointable" onclick="getcurrentuserinfo(\''.StringConverter::ConvertTo1251($user[$i]->GetLogin()).'\')"><td>'.($i+1).'</td><td>'.StringConverter::ConvertTo1251($user[$i]->GetLogin()).'</td><td>'.StringConverter::ConvertTo1251($user[$i]->GetRoleName()).'</td></tr>';
		}
		$content=$content.'</table>';
		return $content;
	}
	//Сборка шаблона по профилю пользователя(для админов)
	static function ConstructCurrentUser($login,$dbw){
		
		$user=new User($dbw->GetUserData($login));
		
		$current_user_inf_labels=array(
				'USER-INFO'=>LKConstructor::ConstructUserTable($user),
				'USER-GROUPS'=>LKConstructor::ConstructCurrentUserGroups($user, $dbw),
				///'USER-RIGHTS-LIST'=>$this->ConstructCurrentUserRights($user, $dbw),
				'USER-LOG'=>'soon'
		);
		
		return Parser::TemplateParse('adminpaneluserotherinf.template.html', $current_user_inf_labels);
	}
	//Блок с добавлением групп пользователю
	static function ConstructCurrentUserGroups(User $user,DBWorker $dbw){
		$groups_user_in=$dbw->GetGroupsUserIn($user->GetID());
		
		$content='<table>';
		for($i=0;$i<count($groups_user_in);$i++){
			if(count($groups_user_in[0])==0)
				break;
				$content=$content.'<tr><td>'.StringConverter::ConvertTo1251($groups_user_in[$i]['name']).'</td><td><img src="images/minus.png"><td></tr>';
		}
		$content=$content.'</table>';
		
		$labels=array(
				'GROUPS'=>$content,
				'GROUPS-SELECTOR'=>LKConstructor::ConstructCurrentUserGroupSelector($user, $dbw)
		);
		return Parser::TemplateParse('lkgroups.template.html', $labels);
	}
	static function ConstructCurrentUserGroupSelector(User $user,DBWorker $dbw){
		$grops_user_not_in=$dbw->GetGroupsUserNotIn($user->GetID());
		
		$content='<select>';
		for($i=0;$i<count($grops_user_not_in);$i++){
			$content=$content.'<option>'.StringConverter::ConvertTo1251($grops_user_not_in[$i]['name']).'</option>';
		}
		$content=$content.'</select>';
		return $content;
	}
	//таблицу с информацией о пользователе
	static function ConstructUserTable(User $user){
		if($user->GetUserReg()==null)
			$user->SetUserReg('???');
			else {
				$dbw=new DBWorker();
				$dbw->ConnectToPostgreSQL();
				$reg_user=$dbw->FingRegUser($user->GetUserReg());
				$user->SetUserReg($reg_user[0]['login']);
			}
			$table_labels=array(
					'LK-LOGIN'=>StringConverter::ConvertTo1251($user->GetLogin()),
					'LK-NAME'=>StringConverter::ConvertTo1251($user->GetName()).' '.StringConverter::ConvertTo1251($user->GetSurname()),
					'LK-DATE'=>$user->GetRegDate(),
					'LK-REGUSER'=>StringConverter::ConvertTo1251($user->GetUserReg()),
					'LK-ROLE'=>StringConverter::ConvertTo1251($user->GetRoleName())
			);
			return Parser::TemplateParse('lktable.template.html', $table_labels);
	}
	static function ConstructUMWAvatar($src=null){
		if($src==null)
			return '<img src="images/userdefault.png">';
			else
				return '<img src="images/'.$src.'">';
	}
	static function ConstructUserRolesForCreate(User $user,DBWorker $dbw){
		$roles_names=$dbw->GetRolesNames();
		$content='<select>';
		$for_i=1;
		if($user->GetUserRole()==1){
			$for_i=0;
		}
		for($i=$for_i;$i<count($roles_names);$i++)
			$content=$content.'<option>'.StringConverter::ConvertTo1251($roles_names[$i]['role_name']).'</option>';
			$content=$content.'</select>';
			return $content;
	}
	static function ConstructRightCurrentGroup(Group $group){
		$content='';
		for($b=0;$b<count($group->GetRights());$b++){
			$right=$group->GetRight($b);
			if($right['create_groups']=='f')
				continue;
			$read_icon='read-disabled';
			$add_icon='add-disabled';
			$delete_icon='delete-disabled';
			//$create_block_flag=false;
			for($r=0;$r<count($group->GetReadRights());$r++){
				if($group->GetReadRight($r)==$right['id']){
					$read_icon='read-enabled';
					$create_block_flag=true;
					break;
				}
			}
			for($a=0;$a<count($group->GetAddRights());$a++){
				if($group->GetAddRight($a)==$right['id']){
					$add_icon='add-enabled';
					break;
				}
			}
			for($d=0;$d<count($group->GetDeleteRights());$d++){
				if($group->GetDeleteRight($d)==$right['id']){
					$delete_icon='delete-enabled';
					break;
				}
			}
			/*
			if($create_block_flag==false)
				continue;*/
			$block='';
			$labels=array(
					'RIGHT-ID'=>$right['id'],
					'RIGHT-NAME'=>StringConverter::ConvertTo1251($right['name']),
					'READ-ACCESS'=>$read_icon,
					'ADD-ACCESS'=>$add_icon,
					'DELETE-ACCESS'=>$delete_icon
			);
			$block=Parser::TemplateParse('specialaccess.template.html', $labels);
			$content=$content.$block;
		}
		
		return $content;
	}
}
?>