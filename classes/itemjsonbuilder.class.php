<?php
//дочерний класс, строит item из имени таблицы и строки json, которая при добавлении получается
class ItemJSONBuilder extends ItemBuilder{
	
	function __construct($table,$jsonstring){
		$jstr=json_decode($jsonstring);
		//echo $jsonstring;
		$this->intable=$table;
		$this->colnames=array();
		$this->values=array();
		for($i=0;$i<count($jstr);$i++){
			if(is_array($jstr[$i])->{'value'}){
				if($jstr[$i]->{'create'}==true){
					$this->values[$i]=array();
					$this->colnames[$i]=$jstr[$i]->{'column'};
					for($j=0;$j<count($jstr[$i]);$j++){
						$this->values[$i][$j]=$jstr[$i]->{'value'}[$j]->{'column'};
						$this->values[$i][$j]=$jstr[$i]->{'value'}[$j]->{'value'};
					}
				}
			}
			else{
				$this->colnames[$i]=$jstr[$i]->{'column'};
				if(is_array(json_decode($jstr[$i]->{'value'})))
					$this->values[$i]=StringConverter::ConvertToPostgreString(json_decode($jstr[$i]->{'value'}));
				else 
					$this->values[$i]=$jstr[$i]->{'value'};
			}
		}
	}

}
?>