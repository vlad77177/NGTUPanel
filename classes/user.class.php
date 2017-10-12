<?php
class User{
	private $id;
	private $login;
	private $password;
	private $name;
	private $surname;
	private $userrole;
	private $rolename;
	private $regdate;
	private $whoreg;
	private $groups;
	
	function __construct($result){
		$this->id=$result['id'];
		$this->login=$result['login'];
		$this->password=$result['pass'];
		$this->name=$result['name'];
		$this->surname=$result['surname'];
		$this->userrole=$result['user_role'];
		$this->regdate=$result['reg_date'];
		$this->whoreg=$result['who_reg'];
		$this->rolename=$result['role_name'];
		$this->groups=StringConverter::GetArrayFromPostgreString($result['groups']);
	}
	
	public function GetID(){
		return $this->id;
	}
	public function GetLogin(){
		return $this->login;
	}
	public function GetPassword(){
		return $this->password;
	}
	public function GetName(){
		return $this->name;
	}
	public function GetSurname(){
		return $this->surname;
	}
	public function GetUserRole(){
		return $this->userrole;
	}
	public function GetRegDate(){
		return $this->regdate;
	}
	public function GetUserReg(){
		return $this->whoreg;
	}
	public function GetGroupsInIDs(){
		return $this->groups;
	}
	public function GetRoleName(){
		return $this->rolename;
	}
	public function SetUserReg($who_reg){
		$this->whoreg=$who_reg;
	}
}
?>