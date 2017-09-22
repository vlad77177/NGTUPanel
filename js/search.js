$(document).ready(function(){
	$('#div-search-filters').find('button').click(function(){
		DownloadBlocks();
	});
});

function DownloadBlocks(name){
	$.ajax({
		url:'getsearch.script.php',
		type:'GET',
		data:{pname:$('#div-search-item-selector select option:selected').data('name')},
		
		success:function(result){
			$('#div-search-data').html(result);
		},
	});
};