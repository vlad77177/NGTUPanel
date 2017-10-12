<?php
class Group{
	private $id;
	
	private $read_rights;
	private $add_rights;
	private $delete_rights;
	
	private $rights;
	
	public function __construct($group_name,DBWorker $dbw){
		$res=$dbw->GetGroupData($group_name);
		$this->id=$res['id'];
		$this->read_rights=StringConverter::GetArrayFromPostgreString($res['read']);
		$this->add_rights=StringConverter::GetArrayFromPostgreString($res['add']);
		$this->delete_rights=StringConverter::GetArrayFromPostgreString($res['delete']);
		
		$this->rights=$dbw->GetRights();
	}
	
	public function GetID(){
		return $this->id;
	}
	public function GetReadRights(){
		return $this->read_rights;
	}
	public function GetReadRight($n){
		return $this->read_rights[$n];
	}
	public function GetAddRights(){
		return $this->add_rights;
	}
	public function GetAddRight($n){
		return $this->add_rights[$n];
	}
	public function GetDeleteRights(){
		return $this->delete_rights;
	}
	public function GetDeleteRight($n){
		return $this->delete_rights[$n];
	}
	public function GetRights(){
		return $this->rights;
	}
	public function GetRight($n){
		return $this->rights[$n];
	}
	
	public function GetRightNameFromId($id){
		for($i=0;$i<count($this->rights);$i++){
			if($this->rights[$i]['id']==$id)
				return $this->rights[$i]['name'];
		}
	}
	
	public function CheckAccessByID($id,$type_access){
		$right_ids='';
		switch($type_access){
			case 'read':{
				$right_ids=$this->read_rights;
				break;
			}
			case 'add':{
				$right_ids=$this->add_rights;
				break;
			}
			case 'delete':{
				$right_ids=$this->delete_rights;
				break;
			}
			default:{
				return false;
			}
		}
		for($i=0;$i<count($right_ids);$i++){
			if($right_ids[$i]==$id)
				return true;
		}
		return false;
	}
}
?>