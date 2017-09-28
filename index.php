<?php 
	session_start();
	//Подключение классов
	include('includes.php');
	//Метки и основной контент
	$labels;
	$content;
	
	$strc=new StringConverter();
	
	$DBW=new DBWorker();
	//Подключение к базе данных
	$DBW->ConnectToPostgreSQL('localhost', '5432', 'postgres', 'postgres','1111');
	$DBW->ConnectToPostgreNGTU('localhost', '5432', 'ngtu', 'postgres', '1111');
	
	//Проверка сессии
	if($DBW->CheckUserLog()==false){
		//Если юзер не залогинился, то парсим страницу с логином
		$content_logging=file_get_contents('login.template.html');
		
		$labels = array(
				'TITLE' => 'Вход',
				'CONTENT' => $content_logging,
		);
		
	}
	else{
		//проверяем, есть ли такой юзер в базе
		$user=$DBW->FindUser($_SESSION['login'], $_SESSION['password']);
		
		$htmlc=new HTMLConstructor();
		
		$DBW->SetUserData($_SESSION['login'],$_SESSION['password']);
		
		$userdata=$DBW->GetUserData();
		
		$userdata_labels=array(
				'UMW-AVATAR'=>$htmlc->ConstructUMWAvatar($userdata[0]['avatar']),
				'UMW-LOGIN'=>$userdata[0]['login'],
				'UMW-NAME'=>StringConverter::ConvertTo1251($userdata[0]['name']).' '.StringConverter::ConvertTo1251($userdata[0]['surname']),
				'UMW-ROLE'=>StringConverter::ConvertTo1251($userdata[0]['role_name']),
		);
		
		
		$header_labels=array(
			'USER-MINI-WINDOW'=>Parser::TemplateParse('userminiwindow.template.html', $userdata_labels),
		);
		
		$content_labels =array(
				'HEADER' =>Parser::TemplateParse('header.template.html', $header_labels),
				'TOP-MENU'=>$htmlc->ConstructMenu($DBW),
				'CENTER'=>$htmlc->ConstructContent($_REQUEST['action'],$DBW),
				'FOOTER'=>'Это футер',
		);
		
		$content=Parser::TemplateParse('content.template.html', $content_labels);
		
		$labels = array(
				'TITLE' => 'Панель управления',
				'CONTENT' => $content,
		);
		
	}
	
	 
	$page = Parser::TemplateParse('template.html', $labels);
	 
	Parser::PageOut($page); 

?>