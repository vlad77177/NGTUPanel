<?php

session_start();

include('classes/dbworker.class.php');

if(isset($_POST['login']) and isset($_POST['password'])){

	$DBW=new DBWorker();
	
	$DBW->ConnectToPostgreSQL('localhost', '5432', 'postgres', 'postgres','1111');
	
	$result=pg_query($DBW->GetPostgreConnection(),'SELECT login,pass FROM users WHERE login=\''.$_POST['login'].'\' AND pass=\''.$_POST['password'].'\'');
	
	$res=pg_fetch_array($result);
	
	if($_POST['login']==$res['login'] and $_POST['password']==$res['pass']){
		
		$_SESSION['login']=$res['login'];
		$_SESSION['password']=$res['pass'];
		
		echo true;
	}
	else
		echo false;
	
}
else{
	echo false;
}
	
?>