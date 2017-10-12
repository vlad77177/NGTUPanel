<?php

class HTMLConstructor{
	//������ �������
	public function ConstructMenu(DBWorker $dbw){
		$menu='';
		$labels='';
		$droptable=false;
		
		if($dbw->CheckLoggedUserAdmin()==true){
			$labels=array('�����','������ � �������','����������� �������','������ �������');
			for($i=0;$i<count($labels);$i++){
				$menu=$menu.'<div class="white-block"><a href="'.'/index.php?action='.($i+1).'">'.$labels[$i].'</a></div>';
			}
		}
		else{
			$labels=array('�����','������ � �������','������ �������');
			for($i=0;$i<2;$i++){
				$menu=$menu.'<div class="white-block"><a href="'.'/index.php?action='.($i+1).'">'.$labels[$i].'</a></div>';
			}
			$menu=$menu.'<div class="white-block"><a href="'.'/index.php?action=4">'.$labels[2].'</a></div>';
		}
		
		
		return $menu;
	}
	//������ �������� �������
	public function ConstructContent($action,DBWorker $dbworker){
		//���������� � ���� ���������
		$content='';
		//�� ��������
		switch($action){
			case 1:{
				$content=$this->ConstructSearchContent($dbworker);
				break;
			}
			case 2:{	
				//������ ���������� ������
				$content=$this->ConstructAddDataContent($dbworker);
				break;
			}
			case 3:{
				//������ ������� � ���������
				$content=TableConstructor::ConstructTableContent($dbworker);
				break;
			}
			case 4:{
				$content=LKConstructor::ConstructLK($dbworker);
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
				//���� ������
				if($pattern->GetColtypes($i)==3 or $pattern->GetColtypes($i)==5 or $pattern->GetColtypes($i)==4){
					//���� ��������� ������
					$displayed=$link->GetColDisplayData();
					$item_ids=array();
					if($pattern->GetColtypes($i)==5 or $pattern->GetColtypes($i)==4){
						$item_ids=StringConverter::GetArrayFromPostgreString($item->GetValues($i));
					}
					else{
						$item_ids[0]=$item->GetValues($i);
					}
					$content=$content.'<td>';
					for($j=0;$j<count($item_ids);$j++){//�.� value ����� ������� ������ ������, �� ������� � ������������
						//$data=$dbw->GetSomeDataFromTable($link->GetTableName(),$link->GetColCreatedData(),$item_ids[$j],'int');
						$data=$dbw->GetNGTUTableRows(
							$link->GetTableName(),
							array($link->GetColCreatedData()),
							array($item_ids[$j]),
							array('int')
						);
						//������� ������ ������
						
						$content=$content.'<a href="index.php?table='
								.$link->GetTableName().'&itemcolname='
								.$link->GetColCreatedData().'&itemid='
								.$data[0][$link->GetColCreatedData()].'">';
						//���� ������ ��� a
						
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
					//���� ������ ������
					$item_array=StringConverter::GetArrayFromPostgreString($item->GetValues($i));//������ ��������
					$content=$content.'<td>';
					for($k=0;$k<count($item_array);$k++){//������������ ��������
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
		$user=new User($dbw->GetUserData());
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
		$user=new User($dbw->GetUserData());
		$pattern=new DataPattern($patt_name, $dbw);
		
		if(SecurityWorker::CheckTheRightOfAdd($user, $pattern, $dbw)==false){
			return '� ��� ��� ���� �� �������� ������';
		}
		
		$content='<table>';
		
		$dbw->ConnectToPostgreNGTU();
		
		/*
		 * $right_id - �����, ���������� ��� ����������
		 * $data_names - �������� �������� �����
		 * $col_names - �������� ������� �������, � ������� ����� �������� ������
		 * $link_id - ������ �� ����� �������( ��� -1 - ��� ������)
		 * $col_types - ���� ������(��������, ��������, ������(������� ����) ��� ������(������ ������� ������))
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
					$link=$pattern->GetLink($i);//������
					$display_column=$link->GetColDisplayData();//�������, ������� ����� ���������� � option
					//$data=$dbw->GetSomeDataFromTable($link->GetTablename());//��� ������ �� ������ �������
					$data=$dbw->GetNGTUTableRows($link->GetTableName());
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
					$link=$pattern->GetLink($i);//������
					$display_column=$link->GetColDisplayData();//�������, ������� ����� ���������� � option
					$data=$dbw->GetNGTUTableRows($link->GetTableName());//��� ������ �� ������ �������
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
					$display_column=$link->GetColDisplayData();//�������, ������� ����� ���������� � option
					$data=$dbw->GetNGTUTableRows($link->GetTableName());//��� ������ �� ������ �������
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
						$content=$content.'<button name="'.$link->GetTableName().'" data-method="create">�������</button>';
					break;
				case 5:
					$content=$content.'<button name="'.$link->GetTableName().'" data-method="add">��������</button>';
					break;
			}
			
			$content=$content.'</td></tr>';
		}
		
		$content=$content.'</table>';
		
		return $content;
	}

}