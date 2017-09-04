$(document).ready(function() {
	$(function(){
        $('#div-logging').find('input[name="submit"]').click(function(e){
        	alert('запуск');
        	checklog(e);
        })
        
});
	
	function checklog(e){
		var data=$('form').serialize();
	    $.ajax({
	        url: "finduser.script.php",
	        type: 'POST',
	        data: data,
	        dataType:'text',
	        async:false,
	        success: function(result) {
	        	if(result==false){
	        		alert('Ошибка! Пользователь не найден!');
	        	}
	        	else{
	        		alert("Пользователь обнаружен!");
	        		document.location.href('index.php');
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
	
})