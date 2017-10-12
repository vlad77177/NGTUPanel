<?php
class DataList{
	private $id;
	private $name;
	private $table;
	private $items;
	
	function __construct(DataListBuilder $dlb){
		$this->id=$dlb->GetID();
		$this->name=$dlb->GetName();
		$this->table=$dlb->GetTable();
		$this->items=$dlb->GetItems();
	}
	
	function GetID(){
		return $this->id;
	}
	function GetName(){
		return $this->name;
	}
	function GetTable(){
		return $this->table;
	}
	function GetItem($n){
		return $this->items[$n];
	}
	function GetItems(){
		return $this->items;
	}
}
?>