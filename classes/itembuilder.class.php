<?php
//абстрактный класс для построителя item, 
abstract class ItemBuilder{
	
	protected $intable;//имя таблицы
	protected $colnames;//массив с названиями столбцов
	protected $values;//массив значений
	
	function GetTable(){
		return $this->intable;
	}
	function GetColumns(){
		return $this->colnames;
	}
	function GetValues(){
		return $this->values;
	}
}
?>