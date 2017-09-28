<?php 
	session_start();
	//����������� �������
	include('includes.php');
	//����� � �������� �������
	$labels;
	$content;
	
	$strc=new StringConverter();
	
	$DBW=new DBWorker();
	//����������� � ���� ������
	$DBW->ConnectToPostgreSQL('localhost', '5432', 'postgres', 'postgres','1111');
	$DBW->ConnectToPostgreNGTU('localhost', '5432', 'ngtu', 'postgres', '1111');
	
	//�������� ������
	if($DBW->CheckUserLog()==false){
		//���� ���� �� �����������, �� ������ �������� � �������
		$content_logging=file_get_contents('login.template.html');
		
		$labels = array(
				'TITLE' => '����',
				'CONTENT' => $content_logging,
		);
		
	}
	else{
		//���������, ���� �� ����� ���� � ����
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
				'FOOTER'=>'��� �����',
		);
		
		$content=Parser::TemplateParse('content.template.html', $content_labels);
		
		$labels = array(
				'TITLE' => '������ ����������',
				'CONTENT' => $content,
		);
		
	}
	
	 
	$page = Parser::TemplateParse('template.html', $labels);
	 
	Parser::PageOut($page); 

?>