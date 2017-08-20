var tablenow=null;	//текущая записть в таблице(в табл-лист)
var tablename=null;	//имя текущей таблицы
var pagenow=null;	//текущая страница(объект)
var rowsinpage=null; //видно записей

var color='#12de2c';	//цвет выбранных элементов

//загрузка ссылок на страницы таблицы(table - имя таблицы, rowinpage - строк на странице из селектеда)

function downloadPages(table,rowsinpage){
	$.ajax({
		url:'gettable.script.php?action=2',
		type:'POST',
		data:{t_name:table,inpage:rowsinpage},
		
		success: function(result){
			$('#div-table-pages').html(result);
			pagenow=$('#div-table-pages div:eq(0)');
			pagenow.css('color',color);
		},
		error: function(jqXHR,exception){
			alert('Ошибка');
		}
	});
};

//при нажитии на сслыку страницы - подгружаем следующий блок таблицы
function nextpage(n){
	var clickedpage=$('#div-table-pages div:eq('+(n-1)+')');
	if(clickedpage.text()!=pagenow.text()){
		checkTable(tablename,n);
		pagenow.css('color','black');
		pagenow=$('#div-table-pages div:eq('+(n-1)+')');
		pagenow.css('color',color);
	}
};

//подгрузка таблицы(name - имя таблицы, page_numb - номер подгружаемых страниц)
function checkTable(name,page_numb,pd){
	rowsinpage=$('#div-table-info select option:selected').text();	//сколько будет видно записей
	$.ajax({
		url:'gettable.script.php?action=1',
		type:'POST',
		data:{t_name:name,p_number:page_numb,offset:rowsinpage},
		
		success: function(result){
			$('#div-table').html(result);
			tablenow.css('color','black');
			tablenow=$('#div-table-list div[name="'+name+'"]');
			tablenow.css('color',color);
			//подгружаем страницы только в случае первой загрузки страницы или загрузки новой таблицы
			if(pagenow==null || tablename!=name){
				downloadPages(name,rowsinpage);
				tablename=name;
			}
		},
		error: function(jqXHR,exception){
			alert('Ошибка'+jqXHR.responseText);
			$('#div-table').html('Ошибка');
		}
	});
};

$(document).ready(function() {
	
	//подгрузка при смене счетчика
	$('#div-table-info select').change(function(){
		//checkTable(tablename,pagenow.text());
		//Временно отключил, надо подумоть
	});
	
	$(function(){
		checkTable($('#div-table-list div:eq(1)').attr('name'),1);
		$('#div-table-list div:eq(1)').css('color',color);
		tablenow=$('#div-table-list div:eq(1)');
	});
	
});