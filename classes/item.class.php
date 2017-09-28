<?php

/*
 * 0 - integer
 * 1 - string
 * 2 - link
 * 3 - links
 * 4 - link(create)
 * 5 - links(create)
 */

class Item{
	
	private $intable;
	private $colnames;
	private $values;
	
	public function __construct(ItemBuilder $ib){
		$this->intable=$ib->GetTable();
		$this->colnames=$ib->GetColumns();
		$this->values=$ib->GetValues();
	}
	
	public function GetTable(){
		return $this->intable;
	}
	
	public function GetColumnNames($n=null){
		if($n===null)
			return $this->colnames;
		else
			return $this->colnames[$n];
	}
	
	public function GetValues($n=null){
		if($n===null)
			return $this->values;
		else
			return $this->values[$n];
	}
	
	public function SetValue($n,$v){
		$this->values[$n]=$v;
	}

}

?>