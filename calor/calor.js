
var calor_setinterval;

function calor() {

	clearInterval(calor_setinterval);

	$.post("calor/mswCalor.php", 
		{ inTemp : $("#inTemp").val() },
		function(data) {
			data = $.parseJSON(data);
			for(var i in data) {
				$("#" + i).text(data[i]);
			}
		}
	);

	calor_setinterval=setInterval(calor, 5000);
}


function calor_close() {
	 $.post("calor/mswCalor.php", { inTemp : "0" } );
	 sleep(850);	
}






$(document).ready(function() {	
	
	$("#btnTemp").click(function() {
		calor();				
	});
	
	
	if($("#fieldCalor").length == 1) {
		calor();
	}

	$(window).unload(function() {
	    $.post("calor/mswCalor.php", { inTemp : "0" } );
		sleep(850);
	});	
	
});


