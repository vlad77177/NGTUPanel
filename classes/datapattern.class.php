<?php

class DataPattern{
	
	private $name;
	private $rights;
	private $datanames;
	private $colnames;
	private $links;
	private $tablename;
	private $coltypes;
	private $cellcount;
	private $neededright;
	private $createfirst;
	
	public function __construct($patternname,DBWorker $dbw){
		$res=$dbw->GetPaternByName($patternname);
		$this->name=$res[0]['data_name'];
		$rights=StringConverter::GetArrayFromPostgreString($res[0]['right_id']);
		$this->rights=array();
		for($i=0;$i<count($rights);$i++){
			$this->rights[$i]=$rights[$i];
		}
		$data_names=StringConverter::GetArrayFromPostgreString($res[0]['data_names']);
		$this->datanames=array();
		for($i=0;$i<count($data_names);$i++){
			$this->datanames[$i]=$data_names[$i];
		}
		$col_names=StringConverter::GetArrayFromPostgreString($res[0]['col_names']);
		$this->colnames=array();
		for($i=0;$i<count($col_names);$i++){
			$this->colnames[$i]=$col_names[$i];
		}
		$linkid=StringConverter::GetArrayFromPostgreString($res[0]['link_id']);
		$this->links=array();
		for($i=0;$i<count($linkid);$i++){
			if($linkid[$i]!=-1)
				$this->links[$i]=new Link($linkid[$i],$dbw);
			else $this->links[$i]=null;
		}
		$this->tablename=$res[0]['table_name'];
		$coltypes=StringConverter::GetArrayFromPostgreString($res[0]['col_types']);
		$this->coltypes=array();
		for($i=0;$i<count($coltypes);$i++){
			$this->coltypes[$i]=$coltypes[$i];
		}
		$this->cellcount=$res[0]['cell_count'];
		$this->neededright=$res[0]['needed_right'];
		$this->createfirst=$res[0]['created_first_flag'];
	}
	
	public function GetName(){
		return $this->name;
	}
	public function GetRights(){
		return $this->rights;
	}
	public function GetDatanames($n=null){
		if($n===null)
			return $this->datanames;
		else return $this->datanames[$n];
	}
	public function GetColnames($n=null){
		if($n===null)
			return $this->colnames;
		else return $this->colnames[$n];
	}
	public function GetLinks(){
		return $this->links;
	}
	public function GetLink($n){
		return $this->links[$n];
	}
	public function GetTableName(){
		return $this->tablename;
	}
	public function GetColtypes($n=null){
		if($n===null)
			return $this->coltypes;
		else return $this->coltypes[$n];
	}
	public function GetCellcount(){
		return $this->cellcount;
	}
	public function GetNeededRights(){
		return $this->neededright;
	}
	public function CreateFirst(){
		return $this->createfirst;
	}
}

?>