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
	
	public function __construct($table,$jsonstring){
		$jstr=json_decode($jsonstring);
		$this->intable=$table;
		$this->colnames=array();
		$this->values=array();
		for($i=0;$i<count($jstr);$i++){
			if(is_array($jstr[$i])->{'value'}){
				$this->values[$i]=array();
				$this->colnames[$i]=$jstr[$i]->{'column'};
				for($j=0;$j<count($jstr[$i]);$j++){				
					$this->values[$i][$j]=$jstr[$i]->{'value'}[$j]->{'column'};
					$this->values[$i][$j]=$jstr[$i]->{'value'}[$j]->{'value'};
				}
			}
			else{
				$this->colnames[$i]=$jstr[$i]->{'column'};
				$this->values[$i]=$jstr[$i]->{'value'};
			}
		}
	}
	
	public function GetTable(){
		return $this->intable;
	}
	
	public function GetColumnNames($n=null){
		if($n==null)
			return $this->colnames;
		else
			return $this->colnames[$n];
	}
	
	public function GetValues($n=null){
		if($n==null)
			return $this->values;
		else
			return $this->values[$n];
	}
	
	public function SetValue($n,$v){
		$this->values[$n]=$v;
	}

}

?>