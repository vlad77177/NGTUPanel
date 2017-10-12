<?php
abstract class DataListBuilder{
	
	protected $id;
	protected $name;
	protected $table;
	protected $items;
	
	function GetID(){
		return $this->id;
	}
	function GetName(){
		return $this->name;
	}
	function GetTable(){
		return $this->table;
	}
	function GetItems(){
		return $this->items;
	}
}
?>