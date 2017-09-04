<?php
class DBWorker{
	
	//Для работы с базой данных
	private $user_login;
	private $user_password;
	
	private $connection_postgre;
	private $connection_ngtu;
	
	private $postgre_name;
	private $ngtu_name;
	
	private $userdata;
	
	public function ConnectToPostgreSQL($host='localhost',$port='5432',$dbname='postgres',$user='postgres',$password=1111){
		
		$connect_string='host='.$host.
			' port='.$port.
			' dbname='.$dbname.
			' user='.$user.
			' password='.$password.'';
		
		$dbconnect=pg_connect($connect_string);
		
		$this->connection_postgre=$dbconnect;
		
		$this->postgre_name=$dbname;
	}
	
	public function ConnectToPostgreNGTU($host='localhost',$port='5432',$dbname='ngtu',$user='postgres',$password=1111){
		
		$connect_string='host='.$host.
		' port='.$port.
		' dbname='.$dbname.
		' user='.$user.
		' password='.$password.'';
		
		$dbconnect=pg_connect($connect_string);
		
		$this->connection_ngtu=$dbconnect;
		
		$this->ngtu_name=$dbname;
	}
	
	public function GetPostgreConnection(){
		return $this->connection_postgre;
	}
	
	public function GetNGTUConnection(){
		return $this->connection_ngtu;
	}
	
	public function FindUser($login,$password){
		return $result=pg_fetch_array(pg_query($this->connection_postgre,
				'SELECT * FROM users WHERE login=\''.$login.'\' AND pass=\''.$password.'\''));
	}
	
	public function FingRegUser($id){
		return $result=pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT * FROM users WHERE id=\''.$id.'\''));
	}
	
	public function SetUserData($login=null,$password=null){
		$l=$login;
		$p=$password;
		if($login==null and $password==null){
			$l=$_SESSION['login'];
			$p=$_SESSION['password'];
		}
		$this->userdata=$result=pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT * FROM users JOIN user_roles ON users.user_role=user_roles.id WHERE login=\''.$l.'\' AND pass=\''.$p.'\''
				));
	}
	
	public function GetUserData($login=null){
		if($login==null)
			return pg_fetch_all(pg_query($this->connection_postgre,
					'SELECT users.*,user_roles.role_type,user_roles.role_name FROM users JOIN user_roles ON users.user_role=user_roles.id WHERE login=\''.$this->userdata[0]['login'].'\''));
		else 
			return pg_fetch_all(pg_query($this->connection_postgre,
					'SELECT users.*,user_roles.role_type,user_roles.role_name FROM users JOIN user_roles ON users.user_role=user_roles.id WHERE login=\''.$login.'\''));
	}
	
	public function CheckUserLog(){
		
		if(empty($_SESSION['login']) and empty($_SESSION['password'])){
			return false;
		}
		else{
			return true;
		}
		
	}
	
	public function CheckLoggedUserAdmin(){
		if($this->userdata[0]['role_type']=='superadm' or $this->userdata[0]['role_type']=='adm')
			return true;
		else
			return false;
	}
	
	public function GetTableNames(){
		$p='public';
		$result=pg_query($this->connection_ngtu,
				'SELECT table_name FROM information_schema.tables WHERE table_schema=\''.$p.'\' ORDER BY table_name');
		
		return pg_fetch_all($result);
	}
	
	public function GetTableColumns($table_name){
		$p='public';
		return $columns=pg_fetch_all(pg_query($this->connection_ngtu,
				'SELECT column_name FROM information_schema.columns WHERE table_name=\''.$table_name.'\' AND table_schema=\''.$p.'\''));
	}
	
	public function GetTableData($table_name,$int_f,$int_l,$column){	
		return pg_fetch_all(pg_query($this->connection_ngtu,
				'SELECT * FROM '.$table_name.' OFFSET '.$int_f.' LIMIT '.$int_l.''));
	}
	
	public function GetNumberRowsOfTable($table_name){
		$result=pg_fetch_all(pg_query($this->connection_ngtu,
				'SELECT COUNT(*) FROM '.$table_name.''));
		return $result[0]['count'];
	}
	
	public function GetRolesNames(){
		return $result=pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT * FROM user_roles'));
	}
	
	public function GetRightsNames(){
		return $result=pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT id,name FROM names_rights'));
	}
	public function AddGroup($name,$r_read,$r_add,$r_delete){
		return $result=pg_query($this->connection_postgre,
				'INSERT INTO groups(name,read,add,delete) VALUES(\''.$name.'\',\''.$r_read.'\',\''.$r_add.'\',\''.$r_delete.'\')');
	}
	public function GetGroupNames(){
		return $result=pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT name FROM groups'));
	}
	public function GetGroupData($gname){
		return $result=pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT * FROM groups WHERE name=\''.$gname.'\''));
	}
	public function AddUser($data){
		
		return $result=pg_query($this->connection_postgre,
					'INSERT INTO users(login,pass,name,surname,user_role,reg_date,who_reg,groups)
					VALUES(\''
					.$data['login'].'\',\''
					.$data['pass'].'\',\''
					.$data['name'].'\',\''
					.$data['surname'].'\','
					.$data['user_role'].',\''
					.$data['reg_date'].'\','
					.$data['who_reg'].',\''
					.$data['groups'].'\')'
				);
	}
	public function GetRoleID($role_name){
		return $result=pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT id FROM user_roles WHERE role_name=\''.$role_name.'\''));
	}
	public function GetAllUsersData(){
		return pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT * FROM users JOIN user_roles ON users.user_role=user_roles.id'));
	}
	public function GetGroupsUserIn($id){
		return pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT * FROM groups WHERE id=ANY(SELECT unnest(groups) FROM users WHERE id='.$id.')'));
	}
	public function GetGroupsUserNotIn($id){
		return pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT * FROM groups WHERE id<>ALL(SELECT unnest(groups) FROM users WHERE id='.$id.')'));
	}
	public function GetSummRightTable($id,$tablename){
		return pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT DISTINCT unnest('.$tablename.') FROM groups WHERE id=ANY(SELECT unnest(groups) FROM users WHERE id='.$id.') ORDER BY unnest('.$tablename.')'));
	}
	public function AddGroupForUser($login,$group_id){
		return pg_fetch_all(pg_query($this->connection_postgre,
				'UPDATE users SET groups=array_append(groups,'.$group_id.') WHERE login=\''.$login.'\''));
	}
	public function DeleteGroup($gname){
		return pg_fetch_all(pg_query($this->connection_postgre,
				'DELETE FROM groups WHERE name=\''.$gname.'\''));
	}
	public function DropUserGroup($gname,$userdata){
		return $result=pg_query($this->connection_postgre,
				'UPDATE users SET groups=array_remove(users.groups,(SELECT id FROM groups WHERE name=\''.$gname.'\')) WHERE id='.$userdata[0]['id'].'');
	}
	public function UpdateGroup($gname,$read,$add,$delete){
		return $result=pg_query($this->connection_postgre,
				'UPDATE groups SET read=\''.$read.'\', add=\''.$add.'\', delete=\''.$delete.'\' WHERE name=\''.$gname.'\'');
	}
	public function GetAddedPatterns($pname=null){
		if($pname==null){
			return pg_fetch_all(pg_query($this->connection_postgre,
					'SELECT * FROM data_patterns'));
		}
		else{
			return pg_fetch_all(pg_query($this->connection_postgre,
					'SELECT * FROM data_patterns WHERE table_name=\''.$pname.'\''));
		}
	}
	public function GetLink($id){
		return pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT * FROM links WHERE id='.$id.''));
	}
	public function GetLinkByName($name){
		return pg_fetch_all(pg_query($this->connection_postgre,
				'SELECT * FROM links WHERE table_name=\''.$name.'\''));
	}
	public function GetSomeDataFromTable($tablename){
		$q='SELECT * FROM '.$tablename.'';
		return pg_fetch_all(pg_query($this->connection_ngtu,
				$q));
	}
	public function GetPaternByName($name,$t_name=false){
		if($t_name==false)
			return pg_fetch_all(pg_query($this->connection_postgre,
					'SELECT * FROM data_patterns WHERE data_name=\''.$name.'\''));
		else if($t_name==true)
			return pg_fetch_all(pg_query($this->connection_postgre,
					'SELECT * FROM data_patterns WHERE table_name=\''.$name.'\''));
	}
	public function AddData($tname,$data,$ct,$col_name='name',$data_name='value',$return=null){
		$q='INSERT INTO '.$tname.'(';
		for($i=0;$i<count($data);$i++){
			$q=$q.$data[$i]->{$col_name};
			if($i!=count($data)-1)
				$q=$q.',';
		}
		$q=$q.') VALUES(';
		echo $ct[$i];
		for($i=0;$i<count($data);$i++){
			if($ct[$i]==1 or $ct[$i]==3)
				$q=$q.$data[$i]->{$data_name};
			else 
				$q=$q.'\''.$data[$i]->{$data_name}.'\'';
			if($i!=count($data)-1)
				$q=$q.',';
		}
		$q=$q.')';
		
		if($return!=null){
			$q=$q.' RETURNING '.$return.'';
		}
		
		echo $q;
		
		return $result=pg_query($this->connection_ngtu,$q);
		//return $result=pg_query($this->connection_ngtu,'SELECT * FROM sotr');
	}
	public function AddRow($table,$data,$column){
		return $result=pg_query($this->connection_ngtu,
				'INSERT INTO '.$table.'('.$column.') VALUES('.$data.')');
	}
	public function FindDataFromNGTU($table,$column,$data,$flag=false){
		if($flag==false){
			echo 'SELECT * FROM '.$table.' WHERE '.$column.'='.$data.'';
			return $result=pg_fetch_all(pg_query($this->connection_ngtu,
					'SELECT * FROM '.$table.' WHERE '.$column.'='.$data.''));
		}
		else{
			echo 'SELECT * FROM '.$table.' WHERE '.$column.'=\''.$data.'\'';
			return $result=pg_fetch_all(pg_query($this->connection_ngtu,
					'SELECT * FROM '.$table.' WHERE '.$column.'=\''.$data.'\''));
		}
	}
}