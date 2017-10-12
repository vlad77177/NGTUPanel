<?php
class DataListResultBuilder extends DataListBuilder{
	function __construct($result){
		$this->id=$result['id'];
		$this->name=$result['name'];
		$this->table=$result['table_name'];
		$this->items=$result['items_ids'];
	}
}
?>