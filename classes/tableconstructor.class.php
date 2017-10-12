<?php
class TableConstructor{
	static function ConstructTable($columns,$data){
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
	
	static function ConstructTableContent(DBWorker $dbw){
		//Получаем имена всех таблиц
		$table_names=$dbw->GetTableNames($dbw->GetNGTUConnection());
		//Строим список
		$table_list=TableConstructor::ConstructTableList($table_names);
		
		$way='table.template.html';
		
		$labels=array(
				'TABLE-LIST'=>$table_list,
		);
		
		return Parser::TemplateParse($way, $labels);		
	}
	
	static function ConstructTableList($table_names){
		
		$content='<div>Таблицы</div>';
		for($i=0;$i<count($table_names);$i++){
			$content=$content.'<div name="'.$table_names[$i]['table_name'].
			'" onclick="checkTable(\''.$table_names[$i]['table_name'].
			'\',1);">'.$table_names[$i]['table_name'].'</div>';
		}
		return $content;
	}
	
	static function ConstructPages($pnumber){
		$pages='';
		for($i=1;$i<=$pnumber;$i++)
			$pages=$pages.'<div onclick="nextpage('.$i.')">'.$i.'</div>';
			return $pages;
	}
}
?>