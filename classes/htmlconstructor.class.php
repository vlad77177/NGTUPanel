<?php

class HTMLConstructor{
	//Строим менюшку
	public function ConstructMenu(){
		$menu='';
		
		$labels=array('Поиск','Работа с данными','Просмотреть таблицы','Личный кабинет');
		
		for($i=0;$i<count($labels);$i++){
			$menu=$menu.'<div class="white-block"><a href="'.'/index.php?action='.($i+1).'">'.$labels[$i].'</a></div>';
		}
		
		return $menu;
	}
	//Строим основной контент
	public function ConstructContent($action,Parser $parser,DBWorker $dbworker){
		//Переменная с всем контентом
		$content='';
		//По реквесту
		switch($action){
			case 1:{
				break;
			}
			case 2:{	
				break;
			}
			case 3:{
				//Строим контент с таблицами
				$content=$this->ConstructTableContent($parser, $dbworker);
				break;
			}
			case 4:{
				$content=$this->ConstructLK($parser, $dbworker);
				break;
			}
			default:{
				return 'Стартовая страница';
			}
		}
		
		return $content;
	}
	
	function ConstructTableList($table_names){
		
		$content='<div>Таблицы</div>';
		for($i=0;$i<count($table_names);$i++){
			$content=$content.'<div name="'.$table_names[$i]['table_name'].
			'" onclick="checkTable(\''.$table_names[$i]['table_name'].
			'\',1);">'.$table_names[$i]['table_name'].'</div>';
		}
		return $content;
	}
	
	function ConstructTable($columns,$data){
		$table='<table>';
		
		$table=$table.'<tr>';
		for($i=0;$i<count($columns);$i++){
			$table=$table.'<td>'.$columns[$i]['column_name'].'</td>';
		}
		$table=$table.'</tr>';
		
		for($i=0;$i<count($data);$i++){
			$table=$table.'<tr>';
			for($j=0;$j<count($data[$i]);$j++){
				$table=$table.'<td>'.$data[$i][$columns[$j]['column_name']].'</td>';
			}
			$table=$table.'</tr>';
		}
		
		$table=$table.'</table>';
		
		return $table;
	}
	
	function ConstructTableContent(Parser $parser,DBWorker $dbw){
		//Получаем имена всех таблиц
		$table_names=$dbw->GetTableNames();
		//Строим список		
		$table_list=$this->ConstructTableList($table_names);
		
		$way='table.template.html';
		
		$labels=array(
				'TABLE-LIST'=>$table_list,
		);
		
		return $parser->TemplateParse($way, $labels);
		
	}
	
	public function ConstructPages($pnumber){
		$pages='';
		for($i=1;$i<=$pnumber;$i++)
			$pages=$pages.'<div onclick="nextpage('.$i.')">'.$i.'</div>';
		return $pages;
	}
	
	public function ConstructUMWAvatar($src){
		if($src==null)
			return '<img src="images/userdefault.png">';
		else 
			return '<img src="images/'.$src.'">';
	}
	
	public function ConstructLK(Parser $parser,DBWorker $dbw){
		$strc=new StringConverter();
		
		$userdata=$dbw->GetUserData();
		
		$admin_content='';
		
		if($userdata[0]['role_type']=='superadm' or $userdata[0]['role_type']=='adm'){			
			$admin_content=file_get_contents('adminpanel.template.html');
		}
		
		$userdata_labels=array(
				'LK-AVATAR'=>$this->ConstructUMWAvatar($userdata[0]['avatar']),
				'LK-TABLE'=>$this->ConstructUserTable($userdata, $parser,$strc),
				'LK-ADMINPANEL-CONTENT'=>$admin_content
		);
		
		return $parser->TemplateParse('lk.template.html', $userdata_labels);
	}
	
	public function ConstructUserRolesForCreate(array $userdata,DBWorker $dbw,StringConverter $strc){
		$roles_names=$dbw->GetRolesNames();
		$content='<select>';
		$for_i=1;
		if($userdata[0]['role_type']=='super'){
			$for_i=0;
		}
		for($i=$for_i;$i<count($roles_names);$i++)
			$content=$content.'<option>'.$strc->ConvertTo1251($roles_names[$i]['role_name']).'</option>';
		$content=$content.'</select>';
		return $content;
	}
	
	public function ConstructRightsForNewGroup(array $nr){
		$content='';
		$strc=new StringConverter();
		$image_no='<img src="images/no.png">';
		$content=$content.'<table>';
		$content=$content.'<tr><td>Роль</td><td>Чтение</td><td>Запись/Редактирование</td><td>Удаление</td></tr>';
		for($i=0;$i<count($nr);$i++){
			$content=$content.'<tr>';
			$content=$content.'<td>'.$strc->ConvertTo1251($nr[$i]['name']).'</td>';
			for($j=0;$j<3;$j++){
				$content=$content.'<td onclick="changeRight('.$i.','.$j.',0,\'new\')">'.$image_no.'</td>';
			}
			$content=$content.'</tr>';
		}
		$content=$content.'</table>';
		return $content;
	}
	
	public function ConstructRightCurrentGroup($nr,$r,$a,$d){
		//Сборка таблицы с готовыми правами
	
		$image_ok='<img src="images/ok.png">';
		$image_no='<img src="images/no.png">';
		
		$name_def='';
		
		$strc=new StringConverter();
		
		$content='<table>';
		
		$for_r=0;
		$for_a=0;
		$for_d=0;
		
		for($i=0;$i<count($nr);$i++){
			$current_img='';
			$content=$content.'<tr>';
			$content=$content.'<td>'.$strc->ConvertTo1251($nr[$i]['name']).'</td>';
			
			if($nr[$i]['id']==$r[$for_r]){
				$current_img=$image_ok;
				$for_r=$for_r+1;
				$name_def='y';
			}
			else {
				$current_img=$image_no;
				$name_def='n';
			}
			$content=$content.'<td name="'.$name_def.'">'.$current_img.'</td>';
			
			if($nr[$i]['id']==$a[$for_a]){
				$current_img=$image_ok;
				$for_a=$for_a+1;
				$name_def='y';
			}
			else{
				$current_img=$image_no;
				$name_def='n';
			}
			$content=$content.'<td name="'.$name_def.'">'.$current_img.'</td>';
			
			if($nr[$i]['id']==$d[$for_d]){
				$current_img=$image_ok;
				$for_d=$for_d+1;
				$name_def='y';
			}
			else{
				$current_img=$image_no;
				$name_def='n';
			}
			$content=$content.'<td name="'.$name_def.'">'.$current_img.'</td>';
			
			$content=$content.'</tr>';
		}
		$content=$content.'</table>';
		return $content;
	}
	
	public function ConstructGroupList($gn){
		if($gn==null)
			return 'Небыло создано еще ни одной группы!';
		else{
			$strc=new StringConverter();
			$content='<table>';
			for($i=0;$i<count($gn);$i++){
				$content=$content.'<tr><td><div onclick="getrights('
						.$i.')" class="class-pointable">'
						.$strc->ConvertTo1251($gn[$i]['name']).'</div></td><td><img src="images/minus.png" onclick="deletegroup(\''
						.$strc->ConvertTo1251($gn[$i]['name']).'\')"></td></tr>';
			}
			$content=$content.'</table>';
			return $content;
		}
	}
	
	public function ConstructUserList(array $userdata){
		$strc=new StringConverter();
		$content='';
		$content=$content.'<table>';
		for($i=0;$i<count($userdata);$i++){
			$content=$content.'<tr name="'.$strc->ConvertTo1251($userdata[$i]['login']).'" class="class-pointable" onclick="getcurrentuserinfo(\''.$strc->ConvertTo1251($userdata[$i]['login']).'\')"><td>'.($i+1).'</td><td>'.$strc->ConvertTo1251($userdata[$i]['login']).'</td><td>'.$strc->ConvertTo1251($userdata[$i]['role_name']).'</td></tr>';
		}
		$content=$content.'</table>';
		return $content;
	}
	//Сборка шаблона по профилю пользователя(для админов)
	public function ConstructCurrentUser($login,$dbw){

		$parser=new Parser();
		$strc=new StringConverter();
		
		$userdata=$dbw->GetUserData($login);
		
		$current_user_inf_labels=array(
				'USER-INFO'=>$this->ConstructUserTable($userdata,$parser,$strc),
				'USER-GROUPS'=>$this->ConstructCurrentUserGroups($userdata, $dbw, $parser, $strc),
				'USER-LOG'=>'soon',
				'USER-RIGHTS-LIST'=>$this->ConstructCurrentUserRights($userdata, $dbw, $parser, $strc)
		);
		
		return $parser->TemplateParse('adminpaneluserotherinf.template.html', $current_user_inf_labels);
	}
	//Визуализация таблицы для конкретного пользователя
	private function ConstructCurrentUserRights($userdata, DBWorker $dbw, $parser, StringConverter $strc){
		$names_rights=$dbw->GetRightsNames();
		
		$read=$strc->GetArrayFromAccArray($dbw->GetSummRightTable($userdata[0]['id'], 'read'),'unnest');
		$add=$strc->GetArrayFromAccArray($dbw->GetSummRightTable($userdata[0]['id'], 'add'),'unnest');
		$delete=$strc->GetArrayFromAccArray($dbw->GetSummRightTable($userdata[0]['id'], 'delete'),'unnest');
		
		return $this->ConstructRightCurrentGroup($names_rights, $read, $add, $delete);
	}
	//Блок с добавлением групп пользователю
	private function ConstructCurrentUserGroups($userdata,DBWorker $dbw,Parser $parser,StringConverter $strc){
		$groups_user_in=$dbw->GetGroupsUserIn($userdata[0]['id']);
		
		$content='';
		for($i=0;$i<count($groups_user_in);$i++){
			$content=$content.'<div>'.$strc->ConvertTo1251($groups_user_in[$i]['name']).'</div>';
		}
		$labels=array(
				'GROUPS'=>$content,
				'GROUPS-SELECTOR'=>$this->ConstructCurrentUserGroupSelector($userdata, $dbw,$strc)
		);
		return $parser->TemplateParse('lkgroups.template.html', $labels);
	}
	private function ConstructCurrentUserGroupSelector($userdata,DBWorker $dbw,StringConverter $strc){
		$grops_user_not_in=$dbw->GetGroupsUserNotIn($userdata[0]['id']);
		
		$content='<select>';
		for($i=0;$i<count($grops_user_not_in);$i++){
			$content=$content.'<option>'.$strc->ConvertTo1251($grops_user_not_in[$i]['name']).'</option>';
		}
		$content=$content.'</select>';
		return $content;
	}
	//таблицу с информацией о пользователе
	private function ConstructUserTable(array $userdata,Parser $parser,StringConverter $strc){
		if($userdata[0]['who_reg']==null)
			$userdata[0]['who_reg']='???';
		else {
			$dbw=new DBWorker();
			$dbw->ConnectToPostgreSQL();
			$reg_user=$dbw->FingRegUser($userdata[0]['who_reg']);
			$userdata[0]['who_reg']=$reg_user[0]['login'];
		}
		$table_labels=array(
				'LK-LOGIN'=>$strc->ConvertTo1251($userdata[0]['login']),
				'LK-NAME'=>$strc->ConvertTo1251($userdata[0]['name']).' '.$strc->ConvertTo1251($userdata[0]['surname']),
				'LK-DATE'=>$userdata[0]['reg_date'],
				'LK-REGUSER'=>$strc->ConvertTo1251($userdata[0]['who_reg']),
				'LK-ROLE'=>$strc->ConvertTo1251($userdata[0]['role_name'])
		);
		return $parser->TemplateParse('lktable.template.html', $table_labels);
	}
	
}