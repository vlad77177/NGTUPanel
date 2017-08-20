var color='#12de2c'; //зеленый
var color_yellow='#ffff00'; 
var color_white='#ffffff'; 

var currentElement=null; //текщий элемент меню(пользователи, группы, логи)
var currentElementUserList=null; //текущий элемент из листа с пользователями
var currentGroup=null;

var currentUserLogin=null;

var right_matrix=null; //права группы(новой)
var right_current_matrix=null; //выбранной группы
var right_current_matrix_new=null; //новые значения
var right_user_matrix=null;
var right_user_matrix_new=null;

var gname_min=3;
var gname_max=30;

// Тут для юзера

function createuser(){//Добавляем
	
	var role=$('#div-admpusermain-create-user table #td-role select option:selected').text();
	var login=$('#div-admpusermain-create-user table input[name="login"]').val();
	var password=$('#div-admpusermain-create-user table input[name="password"]').val();
	var repeat_password=$('#div-admpusermain-create-user table input[name="repeat-password"]').val();
	var name=$('#div-admpusermain-create-user table input[name="name"]').val();
	var surname=$('#div-admpusermain-create-user table input[name="surname"]').val();
	
	if(login=='' || password=='' || repeat_password=='' || name=='' || surname==''){
		alert('Заполните все поля!');
		return;
	}
	
	if(password!=repeat_password){
		alert('Пароли не совпадают!');
		return;
	}
	var userdata={'role':role,'login':login,'password':password,'name':name,'surname':surname};
	$.ajax({
		url:'createuser.script.php',
		type:'POST',
		data:userdata,
		success:function(result){
			if(result==true){
				getcontent(1);
			}
			else
				alert(result);
		},
		error: function(jqXHR,exception){
    		if (jqXHR.status === 0) {
    			alert('НЕ подключен к интернету!');
			} else if (jqXHR.status == 404) {
			alert('НЕ найдена страница запроса [404])');
			} else if (jqXHR.status == 500) {
			alert('НЕ найден домен в запросе [500].');
			} else if (exception === 'parsererror') {
			alert("Ошибка в коде: \n"+jqXHR.responseText);
			} else if (exception === 'timeout') {
			alert('Не ответил на запрос.');
			} else if (exception === 'abort') {
			alert('Прерван запрос Ajax.');
			} else {
			alert('Неизвестная ошибка:\n' + jqXHR.responseText);
			}
    	}
	});
};

function getcurrentuserinfo(login){
	var log=login;
	currentUserLogin=log;
	$.ajax({
		url:'getadmincontent.script.php?action=5',
		type:'POST',
		data:{login:log},
		
		success:function(result){
			$('#div-admpusermain-current-user').html(result);
			if(currentElementUserList==null){
				currentElementUserList=$('#div-admpusermain-list-info table tr[name="'+login+'"]');
				currentElementUserList.css('color',color);
			}
			else{
				currentElementUserList.css('color','black');
				currentElementUserList=$('#div-admpusermain-list-info table tr[name="'+login+'"]');
				currentElementUserList.css('color',color);
			}
			
			right_user_matrix=new Array();
			setMatrix(right_user_matrix,$('#div-admpusermain-current-user-rights table'),'change',0);
			
			//alert(right_user_matrix);
			
			var table=$('#div-admpusermain-current-user-rights table');
			
			for(var i=0;i<$(table).find('tr').length;i++){
				var row=$(table).find('tr:eq('+i+')');
				for(var j=0;j<$(row).find('td').length-1;j++){
					$(row).find('td:eq('+(j+1)+')').click({row:i, column:j, mode:0, mode2:'user-upd'},function(e){
						changeRight(e.data.row,e.data.column,e.data.mode,e.data.mode2);
						//drawRightChanges(right_current_matrix,right_current_matrix_new);
					});
				}
			}

		}
	});
}

function addusergroup(){
	var groupname=$('#div-current-user-groups-button-select select option:selected').text();
	$.ajax({
		url:'changeusergroups.script.php?action=1',
		type:'POST',
		data:{groupname:groupname,username:currentUserLogin},
		
		success:function(result){
			if(result!=1 && result!=2){
				getcurrentuserinfo(currentUserLogin);
			}
		}
	});
}

//Группы:

function deletegroup(name){
	$.ajax({
		url:'deletegroup.script.php',
		type:'POST',
		data:{gname:name},
		
		success:function(result){
			if(result==true)
				getcontent(2);
			else
				alert(result);
		}
	});
}

function getcontent(action){
	
	if(currentElement==null){
		currentElement=$('#div-adminpanel-menu div:eq('+(action-1)+')');
		currentElement.css('color',color);
	}
	else{
		currentElement.css('color','#000000');
		currentElement=$('#div-adminpanel-menu div:eq('+(action-1)+')');
		currentElement.css('color',color);
	}
	
	$.ajax({
		url:'getadmincontent.script.php?action='+action+'',
		dataType:'html',
		success:function(result){
			$('#div-adminpanel-info').html(result);
			if(action==1){
				
				$('#div-admpusermain-create-user table input[name="login"]').keydown(function(e){
					return validDown($(this),5,30,e);
				});
				$('#div-admpusermain-create-user table input[name="password"]').keydown(function(e){
					return validDown($(this),5,20,e);
				});
				$('#div-admpusermain-create-user table input[name="repeat-password"]').keydown(function(e){
					return validDown($(this),5,20,e);
				});
				$('#div-admpusermain-create-user table input[name="name"]').keydown(function(e){
					return validDown($(this),1,30,e);
				});
				$('#div-admpusermain-create-user table input[name="surname"]').keydown(function(e){
					return validDown($(this),1,30,e);
				});
				
				function validDown(element,min,max,event){
					var text=element.val();
					if(text.length<=min)
						element.css('background-color','#ff8080');
					else
						element.css('background-color','#a6ffa6');
					if(text.length>=max && event.keyCode!=8)
						return false;
					return true;
				}
				
			}
			if(action==2){
				
				right_matrix=new Array();
				right_current_matrix=new Array();
				right_current_matrix_new=new Array();
				
				setMatrix(right_matrix,$('#div-adminpanel-info').find('#div-adminpanel-groups-info table'),'new',1);
				
				$('#div-adminpanel-groups-name input[name="name"]').keydown(function(e){
					return validDown($(this),gname_min,gname_max,e);
				});
				
				getrights(0);
			}
		},
		error: function(jqXHR,exception){
    		if (jqXHR.status === 0) {
    			alert('НЕ подключен к интернету!');
			} else if (jqXHR.status == 404) {
			alert('НЕ найдена страница запроса [404])');
			} else if (jqXHR.status == 500) {
			alert('НЕ найден домен в запросе [500].');
			} else if (exception === 'parsererror') {
			alert("Ошибка в коде: \n"+jqXHR.responseText);
			} else if (exception === 'timeout') {
			alert('Не ответил на запрос.');
			} else if (exception === 'abort') {
			alert('Прерван запрос Ajax.');
			} else {
			alert('Неизвестная ошибка:\n' + jqXHR.responseText);
			}
    	}
	});
	
};

function getrights(n){
	currentGroup=$('#div-adminpanel-groups-list-info div:eq('+n+')');
	text=$('#div-adminpanel-groups-list-info div:eq('+n+')').text();
	$.ajax({
		url:'getadmincontent.script.php?action=4',
		type:'POST',
		data:{name:text},
		
		success:function(result){
			$('#div-adminpanel-groups-list-right-info').html(result);
			$('#div-adminpanel-groups-list-info div').css('color','#000000');
			$('#div-adminpanel-groups-list-info div:eq('+n+')').css('color',color);
			
			setMatrix(right_current_matrix,$('#div-adminpanel-groups-list-right-info table'),'current',0);
			setMatrix(right_current_matrix_new,$('#div-adminpanel-groups-list-right-info table'),'change',0);
			
			var table=$('#div-adminpanel-groups-list-right-info table');
			
			for(var i=0;i<$(table).find('tr').length;i++){
				var row=$(table).find('tr:eq('+i+')');
				for(var j=0;j<$(row).find('td').length-1;j++){
					$(row).find('td:eq('+(j+1)+')').click({row:i, column:j, mode:0, mode2:'upd'},function(e){
						changeRight(e.data.row,e.data.column,e.data.mode,e.data.mode2);
						drawRightChanges(right_current_matrix,right_current_matrix_new);
					});
				}
			}
		}
	});
}

function setMatrix(matrix,table,mode,row_start){
	rows=(table).find('tr').length;
	for(var i=0;i<rows-row_start;i++){
		matrix[i]=new Array();
		for(var j=0;j<3;j++){
			if(mode=='current' || mode=='change'){
				cell=(table).find('tr:eq('+i+') td:eq('+(j+1)+')');
				if(cell.attr('name')=='y')
					matrix[i][j]=true;
				else
					matrix[i][j]=false;
			}
			else
				matrix[i][j]=false;
		}
	}
}

$(document).ready(function(){
	
	getcontent(1);
	
});

function validDown(element,min,max,event){
	var text=element.val();
	if(text.length<=min)
		element.css('background-color','#ff8080');
	else
		element.css('background-color','#a6ffa6');
	if(text.length>=max && event.keyCode!=8)
		return false;
	return true;
}

//Устанавливает галочки и крестики для добавлния новой группы или изменения уже существвующей
function changeRight(row,column,mode,mode2){
	
	//alert(row+' '+column+' '+mode+' '+mode2);
	
	var matrix='';
	var div_link='';
	var add_index=1;
	
	if(mode2=='upd'){
		matrix=right_current_matrix_new;
		div_link='#div-adminpanel-groups-list-right-info';
		add_index=0;
	}
	else if(mode2=='new'){
		matrix=right_matrix;
		div_link='#div-adminpanel-groups-info';
	}
	else{
		matrix=right_user_matrix;
		div_link='#div-admpusermain-current-user-rights';
		add_index=0;
	}
	
	//mode==0 - первое включение
	
	if(mode==0){
		if(matrix[row][column]==false){
			matrix[row][column]=true;
			$(''+div_link+' table tr:eq('+(row+add_index)+') td:eq('+(column+1)+') img').attr('src','images/ok.png');
			if(column>0){
				changeRight(row,column-1,1,mode2);
			}
		}
		else{
			matrix[row][column]=false;
			$(''+div_link+' table tr:eq('+(row+add_index)+') td:eq('+(column+1)+') img').attr('src','images/no.png');
			if(column<3){
				changeRight(row,column+1,2,mode2);
			}
		}
	}
	else{
		//mode==1 - рисуем тру
		if(mode==1){
			matrix[row][column]=true;
			$(''+div_link+' table tr:eq('+(row+add_index)+') td:eq('+(column+1)+') img').attr('src','images/ok.png');
			if(column>0){
				changeRight(row,column-1,1,mode2);
			}
		}
		else{
			matrix[row][column]=false;
			$(''+div_link+' table tr:eq('+(row+add_index)+') td:eq('+(column+1)+') img').attr('src','images/no.png');
			if(column<3){
				changeRight(row,column+1,2,mode2);
			}
		}
	}
}

function drawRightChanges(mcr,mcrn){
	var element='#div-adminpanel-groups-list-right-info';
	var changed=false;
	for(var i=0;i<mcr.length;i++){
		for(var j=0;j<3;j++){
			if(mcr[i][j]!=mcrn[i][j]){
				$(element+' table tr:eq('+i+') td:eq('+(j+1)+')').css('background-color',color_yellow);
				changed=true;
			}
			else{
				$(element+' table tr:eq('+i+') td:eq('+(j+1)+')').css('background-color',color_white);
			}
		}
	}
	if(changed==true){
		if($(element+' *').is('button')==false){
			$(element).append('<button>Сохранить изменения</button>');
			$(element+' button').click({name:currentGroup.text()},function(e){
				updateGroup(e.data.name,right_current_matrix_new);
			});
		}
	}
	else{
		$(element+' button').remove();
	}
}

function updateGroup(name,matrix){
	var newright=JSON.stringify(matrix);
	$.ajax({
		url:'updategroup.script.php',
		data:{gname:name,nright:newright},
		type:'POST',
		
		success:function(result){
			if(result==true)
				getcontent(2);
			else
				alert(result);
		}
	})
}

function createGroup(){
	var name=$('#div-adminpanel-groups-name input').val();
	if(name.length<=gname_min+1 || name.length>gname_max){
		alert('Длина имени не менее '+gname_min+'слов!');
	}
	else{
		var rights=JSON.stringify(right_matrix);
		$.ajax({
			url:'addgroup.script.php',
			data:{name:name,rights:rights},
			type:'POST',
			
			success:function(result){
				if(result==true)
					getcontent(2);
				else
					alert(result);
			}
		});
	}
}