$(document).ready(function(){
	var selector=$('#div-add-data-selector select');
	$(selector).change(function(e){
		getdataaddedblock();
	})
});

function getdataaddedblock(){
	var pattern_name=$('#div-add-data-selector select option:selected').data('table');
	$.ajax({
		url:'getadddatablock.script.php',
		type:'GET',
		data:{patternname:pattern_name},
		
		success: function(result){
			$('#div-add-data-fields').html(result);
			if($('#div-add-data-fields').find('table').length>0){
				$('#div-add-data-fields').find('button[name="create"]').remove();
				$('#div-add-data-fields').append('<button name="create">Создать запись</button>');
				$('#div-add-data-fields').find('button[name="create"]').click(function(e){
					e.preventDefault();
					addData();
				});
				createEventsForButtons();
			}
		}
	})
}

function addData(){
	var tname=$('#div-add-data-selector select option:selected').data('table');
	var data=new Array();
	for(var i=0;i<$('#div-add-data-fields').find('table:eq(0) tr').length;i++){
		var cell=$('#div-add-data-fields').find('table tr:eq('+i+') td:eq(1)');
		if($(cell).find('input').length>0){
			data[i]={"column":$(cell).find('input').data('column'),"value":$(cell).find('input').val()};
		}
		else if($(cell).find('select').length>0){
			if($(cell).find('select').data('type')=='def'){
				data[i]={"column":$(cell).find('select').data('column'),"value":$(cell).find('select option:selected').data('value')};
			}
			else if($(cell).find('select').data('type')=='multiple'){
				var selected=new Array();
				for(var j=0;j<$(cell).find('select option:selected').length;j++){
					selected[j]=$(cell).find('select option:selected:eq('+j+')').data('column');
				}
				data[i]={"column":$(cell).find('select').data('column'),"value":JSON.stringify(selected)};
			}
		}
		else if($(cell).find('div').length>0){
			data[i]={"column":$(cell).find('div').data('column'),"value":getDataFromList(cell),"create":true};
		}
	}
	
	alert(JSON.stringify(data));
	
	$.ajax({
		url:'add.data.script.php',
		type:'POST',
		data:{tablename:tname,tdata:JSON.stringify(data)},
		
		success:function(result){
			alert(result);
		}
	})
	
}

function getDataFromList(cell){
	var value=new Array();
	var div=$(cell).find('div');
	for(var j=0;j<$(div).find('ul').length;j++){
		var ularr=new Array();
		var ul=$(div).find('ul:eq('+j+')');
		for(var k=0;k<$(ul).find('li').length;k++){
			ularr[k]={"column":$(ul).find('li:eq('+k+')').data('column'),"value":$(ul).find('li:eq('+k+')').data('value')};
		}
		value[j]=ularr;
	}
	return value;
}

function createEventsForButtons(){
	var table=$('#div-add-data-fields table');
	for(var i=0;i<$(table).find('button').length;i++){
		var button=$(table).find('button:eq('+i+')');
		$(button).click({blockname:$(button).attr('name'),method:$(button).data('method')},function(e){
			constructNewBlock(e.data.blockname,e.data.method);
		});
	}
}

function constructNewBlock(blockname,method){
	$.ajax({
		url:'getadddatablock.script.php',
		type:'GET',
		data:{patternname:blockname},
		
		success: function(result){
			$('#div-add-data-fields').append('<div name="secondblock">'+result+'</div>');
			var currentBlock=$('#div-add-data-fields div[name="secondblock"]');
			if(method=='add'){
				$(currentBlock).append('<button>Добавить данные</button>');
				$(currentBlock).find('button').click(function(e){
					alert('click');
					var div=$('#div-add-data-fields table').find('div[data-column="'+blockname+'"]');
					var li='<ul>';
					for(var i=0;i<$('#div-add-data-fields table:eq(1) tr').length;i++){
						var td=$('#div-add-data-fields table:eq(1) tr:eq('+i+') td:eq(1)');
						if($(td).find('input').length>0){
							li=li+'<li data-column="'+$(td).find('input').data('column')+'" data-value="'+$(td).find('input').val()+'">'+$(td).find('input').val()+'</li>';
						}
						else if($(td).find('select').length>0){
							li=li+'<li data-column="'+$(td).find('select').data('column')+'" data-value="'+$(td).find('select option:selected').data('value')+'">'+$(td).find('select option:selected').val()+'</li>';
						}
					}
					li=li+'</ul>';
					alert(li);
					$(div).append(li);
				})
			}
		}
	})
}