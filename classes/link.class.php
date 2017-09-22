<?php

class Link{
	private $tablename;
	private $colcreateddata;
	private $coldisplaydata;
	private $creatednew;
	
	public function __construct($linkid,DBWorker $dbw){
		$res=$dbw->GetLink($linkid);
		$this->tablename=$res[0]['table_name'];
		$this->colcreateddata=$res[0]['col_created_data'];
		$cdd=StringConverter::GetArrayFromPostgreString($res[0]['col_display_data']);
		$this->coldisplaydata=array();
		for($i=0;$i<count($cdd);$i++){
			$this->coldisplaydata[$i]=$cdd[$i];
		}
		$this->creatednew=$res[0]['created_new'];
	}
	public function GetTableName(){
		return $this->tablename;
	}
	public function GetColCreatedData(){
		return $this->colcreateddata;
	}
	public function GetColDisplayData(){
		return $this->coldisplaydata;
	}
	public function CreateNew(){
		return $this->creatednew;
	}
}

?>