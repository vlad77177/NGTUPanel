<?php
//дочерний класс, строит item из строки и шаблона
class ItemResultBuilder extends ItemBuilder{
	function __construct($resultrow,DataPattern $dp){
		$this->intable=$dp->GetTableName();
		$this->colnames=array();
		$this->values=array();
		for($i=0;$i<count($dp->GetColnames());$i++){
			$this->colnames[$i]=$dp->GetColnames($i);
			$this->values[$i]=$resultrow[$this->colnames[$i]];
		}
	}	
}
?>