<?php

class HTMLConstructor{
	//Строим менюшку
	public function ConstructMenu(DBWorker $dbw){
		$menu='';
		$labels='';
		$droptable=false;
		
		if($dbw->CheckLoggedUserAdmin()==true){
			$labels=array('Поиск','Работа с данными','Просмотреть таблицы','Личный кабинет');
			for($i=0;$i<count($labels);$i++){
				$menu=$menu.'<div class="white-block"><a href="'.'/index.php?action='.($i+1).'">'.$labels[$i].'</a></div>';
			}
		}
		else{
			$labels=array('Поиск','Работа с данными','Личный кабинет');
			for($i=0;$i<2;$i++){
				$menu=$menu.'<div class="white-block"><a href="'.'/index.php?action='.($i+1).'">'.$labels[$i].'</a></div>';
			}
			$menu=$menu.'<div class="white-block"><a href="'.'/index.php?action=4">'.$labels[2].'</a></div>';
		}
		
		
		return $menu;
	}
	//Строим основной контент
	public function ConstructContent($action,DBWorker $dbworker){
		//Переменная с всем контентом
		$content='';
		//По реквесту
		switch($action){
			case 1:{
				$content=$this->ConstructSearchContent($dbworker);
				break;
			}
			case 2:{	
				//Строим добавление данных
				$content=$this->ConstructAddDataContent($dbworker);
				break;
			}
			case 3:{
				//Строим контент с таблицами
				$content=$this->ConstructTableContent($dbworker);
				break;
			}
			case 4:{
				$content=$this->ConstructLK($dbworker);
				break;
			}
			default:{
				if($_REQUEST['table']!=null and $_REQUEST['itemcolname']!=null and $_REQUEST['itemid']!=null){
					$pattern=new DataPattern($_REQUEST['table'], $dbworker);
					$data=$dbworker->GetNGTUTableRows($pattern->GetTableName(),array($_REQUEST['itemcolname']),array($_REQUEST['itemid']),array('int'));
					$item=new Item(new ItemResultBuilder($data[0], $pattern));
					$content='<div class="white-block" style="width:100%">'.$this->ConstructResultBlock($item, $pattern, $dbworker).'</div>';
				}
			}
		}
		
		return $content;
	}
	
	private function ConstructSearchContent(DBWorker $dbw){
		$search_labels=array(
				'SEARCH-ITEM-SELECTOR'=>$this->ConstructSearchItemsSelector($dbw),
		);
		return Parser::TemplateParse('search.template.html', $search_labels);
	}
	
	public function ConstructResultBlock(Item $item,DataPattern $pattern,DBWorker $dbw){
		$block_labels=array(
				'RESULT-NAME'=>StringConverter::ConvertTo1251($pattern->GetName()),
				'RESULT-INFO'=>$this->ConstructResultInfo($item, $pattern,$dbw)
		);
		return Parser::TemplateParse('resultblock.template.html', $block_labels);
	}
	
	private function ConstructResultInfo(Item $item,DataPattern $pattern,DBWorker $dbw){
		$content='<table>';
		for($i=0;$i<$pattern->GetCellcount();$i++){
			$content=$content.'<tr><td>'.StringConverter::ConvertTo1251($pattern->GetDatanames($i)).':</td>';
			$link=$pattern->GetLink($i);
			if($link==null){
				$content=$content.'<td>'.StringConverter::ConvertTo1251($item->GetValues($i)).'</td></tr>';
			}
			else{
				//если ссылка
				if($pattern->GetColtypes($i)==3 or $pattern->GetColtypes($i)==5 or $pattern->GetColtypes($i)==4){
					//если одиночная ссылка
					$displayed=$link->GetColDisplayData();
					$item_ids=array();
					if($pattern->GetColtypes($i)==5 or $pattern->GetColtypes($i)==4){
						$item_ids=StringConverter::GetArrayFromPostgreString($item->GetValues($i));
					}
					else{
						$item_ids[0]=$item->GetValues($i);
					}
					$content=$content.'<td>';
					for($j=0;$j<count($item_ids);$j++){//т.к value может хранить массив ссылок, то сделаем и перечисление
						$data=$dbw->GetSomeDataFromTable($link->GetTableName(),$link->GetColCreatedData(),$item_ids[$j],'int');
						//получил нужную строку
						
						$content=$content.'<a href="index.php?table='
								.$link->GetTableName().'&itemcolname='
								.$link->GetColCreatedData().'&itemid='
								.$data[0][$link->GetColCreatedData()].'">';
						//выше ссылка для a
						
						for($k=0;$k<count($displayed);$k++){
							$content=$content.StringConverter::ConvertTo1251($data[0][$displayed[$k]]);
							if($k<count($displayed)-1)
								$content=$content.' ';
						}
						$content=$content.'</a>';
						if($j<count($item_ids)-1)
							$content=$content.', ';
					}
					$content=$content.'</td></tr>';
				}/*
				else if($pattern->GetColtypes($i)==5){
					//если массив ссылок
					$item_array=StringConverter::GetArrayFromPostgreString($item->GetValues($i));//массив индексов
					$content=$content.'<td>';
					for($k=0;$k<count($item_array);$k++){//перечисление индексов
						for($j=0;$j<count($item_array);$j++){
							$data=$dbw->GetSomeDataFromTable($link->GetTableName(),$link->GetColCreatedData(),$item_array[$j],'int');
							$content=$content.StringConverter::ConvertTo1251($data[0][$link->ColDisplayData($j)]);
							if($j<count($displayed)-1)
								$content=$content.' ';
						}
						if($k<count($item->GetValues($i))-1)
							$content=$content.',';
					}
					$content=$content.'</td></tr>';
				}*/
			}
			
		}
		return $content=$content.'</table>';
	}
	
	private function ConstructSearchItemsSelector(DBWorker $dbw){
		$content='<select>';
		$data_patterns=$dbw->GetAddedPatterns();
		$user=$dbw->GetUserData();
		for($i=0;$i<count($data_patterns);$i++){
			$content=$content.'<option data-name="'.StringConverter::ConvertTo1251($data_patterns[$i]['table_name']).'">'.StringConverter::ConvertTo1251($data_patterns[$i]['data_name']).'</option>';
		}
		$content=$content.'</select>';
		return $content;
	}
	
	function ConstructAddDataContent(DBWorker $dbw){
		$data_patterns=$dbw->GetAddedPatterns();
		$addc_labels=array(
				'ADD-DATA-SELECTOR'=>$this->ConstructListAddedItems($data_patterns),
		);
		return Parser::TemplateParse('adddata.template.html', $addc_labels);
	}
	
	private function ConstructListAddedItems($dp){
		$content='<select>';
		for($i=0;$i<count($dp);$i++){
			if($dp[$i]['created_first_flag']=='t')
				$content=$content.'<option data-table="'.$dp[$i]['table_name'].'">'.StringConverter::ConvertTo1251($dp[$i]['data_name']).'</option>';
		}
		return $content=$content.'</select>';
	}
	
	public function ConstructAddDataBlock($patt_name,DBWorker $dbw){
		$user=$dbw->GetUserData();
		$pattern=new DataPattern($patt_name, $dbw);
		
		if(SecurityWorker::CheckTheRightOfAdd($user, $pattern, $dbw)==false){
			return 'У вас нет прав на создание записи';
		}
		
		$content='<table>';
		
		$dbw->ConnectToPostgreNGTU();
		
		/*
		 * $right_id - права, неоходимые для добавления
		 * $data_names - названия вводимых полей
		 * $col_names - название колонок таблицы, в которые будут занесены данные
		 * $link_id - ссылки на друге таблицы( где -1 - нет ссылки)
		 * $col_types - типы данных(числовые, строчные, ссылка(внешний ключ) или список(массив внешних ключей))
		 */
		
		/*
		$right_id=StringConverter::GetArrayFromPostgreString($data_pattern[0]['right_id']);
		$data_names=StringConverter::GetArrayFromPostgreString($data_pattern[0]['data_names']);
		$col_names=StringConverter::GetArrayFromPostgreString($data_pattern[0]['col_names']);
		$link_id=StringConverter::GetArrayFromPostgreString($data_pattern[0]['link_id']);
		$col_types=StringConverter::GetArrayFromPostgreString($data_pattern[0]['col_types']);
		$needed_right=StringConverter::GetArrayFromPostgreString($data_pattern[0]['needed_right']);*/
		
		for($i=0;$i<$pattern->GetCellcount();$i++){
			
			$content=$content.'<tr><td>'.StringConverter::ConvertTo1251($pattern->GetDatanames($i)).':</td><td>';
			
			switch($pattern->GetColtypes($i)){
				case 1:{
					$content=$content.'<input type="text" data-display="integer" data-column="'.$pattern->GetColnames($i).'">';
					break;
				}
				case 2:{
					$content=$content.'<input type="text" data-display="string" data-column="'.$pattern->GetColnames($i).'">';
					break;
				}
				case 3:{
					$link=$pattern->GetLink($i);//ссылка
					$display_column=$link->GetColDisplayData();//колонки, которые нужно отобразить в option
					$data=$dbw->GetSomeDataFromTable($link->GetTablename());//все данные из нужной таблицы
					$content=$content.'<select data-column="'.$pattern->GetColnames($i).'" data-type="def">';
					for($j=0;$j<count($data);$j++){
						$content=$content.'<option data-value="'.$data[$j][$link->GetColcreatedData()].'">';
						for($k=0;$k<count($display_column);$k++){
							$content=$content.StringConverter::ConvertTo1251($data[$j][$display_column[$k]]).' ';
						}
						$content=$content.'</option>';
					}
					$content=$content.'</select>';
					break;
				}
				case 4:{
					$link=$pattern->GetLink($i);//сслыка
					$display_column=$link->GetColDisplayData();//колонки, которые нужно отобразить в option
					$data=$dbw->GetSomeDataFromTable($link->GetTableName());//все данные из нужной таблицы
					$content=$content.'<select multiple size="3" data-column="'.$link->GetTableName().'" data-type="multiple">';
					for($j=0;$j<count($data);$j++){
						$content=$content.'<option data-column="'.$data[$j][$link->GetColCreatedData()].'">';
						for($k=0;$k<count($display_column);$k++){
							$content=$content.StringConverter::ConvertTo1251($data[$j][$display_column[$k]]).' ';
						}
						$content=$content.'</option>';
					}
					$content=$content.'</select>';
					break;
				}
				case 5:{
					$link=$pattern->GetLink($i);
					$display_column=$link->GetColDisplayData();//колонки, которые нужно отобразить в option
					$data=$dbw->GetSomeDataFromTable($link->GetTableName());//все данные из нужной таблицы
					$content=$content.'<div data-column="'.$link->GetTableName().'">';
					$content=$content.'</div>';
					break;
				}
			}
			
			$content=$content.'</td><td>';
			
			switch($pattern->GetColtypes($i)){
				case 3;
				case 4;
					$link=$pattern->GetLink($i);
					if($link->CreateNew()=='t')
						$content=$content.'<button name="'.$link->GetTableName().'" data-method="create">Создать</button>';
					break;
				case 5:
					$content=$content.'<button name="'.$link->GetTableName().'" data-method="add">Добавить</button>';
					break;
			}
			
			$content=$content.'</td></tr>';
		}
		
		$content=$content.'</table>';
		
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
				$table=$table.'<td>'.StringConverter::ConvertTo1251($data[$i][$columns[$j]['column_name']]).'</td>';
			}
			$table=$table.'</tr>';
		}
		
		$table=$table.'</table>';
		
		return $table;
	}
	
	function ConstructTableContent(DBWorker $dbw){
		//Получаем имена всех таблиц
		$table_names=$dbw->GetTableNames();
		//Строим список		
		$table_list=$this->ConstructTableList($table_names);
		
		$way='table.template.html';
		
		$labels=array(
				'TABLE-LIST'=>$table_list,
		);
		
		return Parser::TemplateParse($way, $labels);
		
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
	
	public function ConstructLK(DBWorker $dbw){
		
		$userdata=$dbw->GetUserData();
		
		$admin_content='';
		
		if($userdata[0]['role_type']=='superadm' or $userdata[0]['role_type']=='adm'){			
			$admin_content=file_get_contents('adminpanel.template.html');
		}
		
		$userdata_labels=array(
				'LK-AVATAR'=>$this->ConstructUMWAvatar($userdata[0]['avatar']),
				'LK-TABLE'=>$this->ConstructUserTable($userdata),
				'LK-ADMINPANEL-CONTENT'=>$admin_content
		);
		
		return Parser::TemplateParse('lk.template.html', $userdata_labels);
	}
	
	public function ConstructUserRolesForCreate(array $userdata,DBWorker $dbw){
		$roles_names=$dbw->GetRolesNames();
		$content='<select>';
		$for_i=1;
		if($userdata[0]['role_type']=='super'){
			$for_i=0;
		}
		for($i=$for_i;$i<count($roles_names);$i++)
			$content=$content.'<option>'.StringConverter::ConvertTo1251($roles_names[$i]['role_name']).'</option>';
		$content=$content.'</select>';
		return $content;
	}
	
	public function ConstructRightsForNewGroup(array $nr){
		$content='';
		$image_no='<img src="images/no.png">';
		$content=$content.'<table>';
		$content=$content.'<tr><td>Роль</td><td>Чтение</td><td>Запись/Редактирование</td><td>Удаление</td></tr>';
		for($i=0;$i<count($nr);$i++){
			$content=$content.'<tr>';
			$content=$content.'<td>'.StringConverter::ConvertTo1251($nr[$i]['name']).'</td>';
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
		
		$content='<table>';
		
		$for_r=0;
		$for_a=0;
		$for_d=0;
		
		for($i=0;$i<count($nr);$i++){
			$current_img='';
			$content=$content.'<tr>';
			$content=$content.'<td>'.StringConverter::ConvertTo1251($nr[$i]['name']).'</td>';
			
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
	
	public function ConstructUserList(array $userdata){
		$content='';
		$content=$content.'<table>';
		for($i=0;$i<count($userdata);$i++){
			$content=$content.'<tr name="'.StringConverter::ConvertTo1251($userdata[$i]['login']).'" class="class-pointable" onclick="getcurrentuserinfo(\''.StringConverter::ConvertTo1251($userdata[$i]['login']).'\')"><td>'.($i+1).'</td><td>'.StringConverter::ConvertTo1251($userdata[$i]['login']).'</td><td>'.StringConverter::ConvertTo1251($userdata[$i]['role_name']).'</td></tr>';
		}
		$content=$content.'</table>';
		return $content;
	}
	//Сборка шаблона по профилю пользователя(для админов)
	public function ConstructCurrentUser($login,$dbw){
		
		$userdata=$dbw->GetUserData($login);
		
		$current_user_inf_labels=array(
				'USER-INFO'=>$this->ConstructUserTable($userdata),
				'USER-GROUPS'=>$this->ConstructCurrentUserGroups($userdata, $dbw),
				'USER-LOG'=>'soon',
				'USER-RIGHTS-LIST'=>$this->ConstructCurrentUserRights($userdata, $dbw)
		);
		
		return Parser::TemplateParse('adminpaneluserotherinf.template.html', $current_user_inf_labels);
	}
	//Визуализация таблицы для конкретного пользователя
	private function ConstructCurrentUserRights($userdata, DBWorker $dbw){
		$names_rights=$dbw->GetRightsNames();
		
		$read=StringConverter::GetArrayFromAccArray($dbw->GetSummRightTable($userdata[0]['id'], 'read'),'unnest');
		$add=StringConverter::GetArrayFromAccArray($dbw->GetSummRightTable($userdata[0]['id'], 'add'),'unnest');
		$delete=StringConverter::GetArrayFromAccArray($dbw->GetSummRightTable($userdata[0]['id'], 'delete'),'unnest');
		
		return $this->ConstructRightCurrentGroup($names_rights, $read, $add, $delete);
	}
	//Блок с добавлением групп пользователю
	private function ConstructCurrentUserGroups($userdata,DBWorker $dbw){
		$groups_user_in=$dbw->GetGroupsUserIn($userdata[0]['id']);
		
		$content='<table>';
		for($i=0;$i<count($groups_user_in);$i++){
			if(count($groups_user_in[0])==0)
				break;
			$content=$content.'<tr><td>'.StringConverter::ConvertTo1251($groups_user_in[$i]['name']).'</td><td><img src="images/minus.png"><td></tr>';
		}
		$content=$content.'</table>';
		
		$labels=array(
				'GROUPS'=>$content,
				'GROUPS-SELECTOR'=>$this->ConstructCurrentUserGroupSelector($userdata, $dbw)
		);
		return Parser::TemplateParse('lkgroups.template.html', $labels);
	}
	private function ConstructCurrentUserGroupSelector($userdata,DBWorker $dbw){
		$grops_user_not_in=$dbw->GetGroupsUserNotIn($userdata[0]['id']);
		
		$content='<select>';
		for($i=0;$i<count($grops_user_not_in);$i++){
			$content=$content.'<option>'.StringConverter::ConvertTo1251($grops_user_not_in[$i]['name']).'</option>';
		}
		$content=$content.'</select>';
		return $content;
	}
	//таблицу с информацией о пользователе
	private function ConstructUserTable(array $userdata){
		if($userdata[0]['who_reg']==null)
			$userdata[0]['who_reg']='???';
		else {
			$dbw=new DBWorker();
			$dbw->ConnectToPostgreSQL();
			$reg_user=$dbw->FingRegUser($userdata[0]['who_reg']);
			$userdata[0]['who_reg']=$reg_user[0]['login'];
		}
		$table_labels=array(
				'LK-LOGIN'=>StringConverter::ConvertTo1251($userdata[0]['login']),
				'LK-NAME'=>StringConverter::ConvertTo1251($userdata[0]['name']).' '.StringConverter::ConvertTo1251($userdata[0]['surname']),
				'LK-DATE'=>$userdata[0]['reg_date'],
				'LK-REGUSER'=>StringConverter::ConvertTo1251($userdata[0]['who_reg']),
				'LK-ROLE'=>StringConverter::ConvertTo1251($userdata[0]['role_name'])
		);
		return Parser::TemplateParse('lktable.template.html', $table_labels);
	}
	
}