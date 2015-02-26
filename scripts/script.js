function liga(val) {
	$.post("/quadro/mswQuadro.php?c=l"+val);
}

function desliga(val){
	$.post("/quadro/mswQuadro.php?c=d"+val);				
}	

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

timer = null;

function calor() {
	$.post("/calor/mswCalor.php", 
		{ inTemp : $("#inTemp").val() },
		function(data) {
			data = $.parseJSON(data);
			for(var i in data) {
				$("#" + i).text(data[i]);
			}
		}
	);
	
	clearTimeout(timer);
	timer = setTimeout("calor();", 5000);	
}

function aero() {
	$.post("/aero/mswAero.php",
		{yel : $("#yel").val(), red : $("#red").val()});
}

function tempAero() {
		$.post("/aero/mswAero.php", 
			function(data) {
				data = $.parseJSON(data);
				for(var i in data) {
					$("#" + i).text(data[i]);
				}
			}
		);
		
		clearTimeout(timer);
		timer = setTimeout("tempAero()", 5000);
}



function checkKeys() {
	var keys = {};
	$(".chaves").each(function() {
		var keyId = $(this).attr("id").substr(6, 1);
		if($(this).hasClass("hide") && $(this).hasClass("chave-on")) {conducao/conducao.php
			keys[keyId] = false;	
		} else {
			keys[keyId] = true;
		}		
	});
	
	$(".lamp").removeClass("lamp-on").addClass("lamp-off"); //apaga todas
	
	if(keys[1] && keys[2]) {
		if(keys[3] && !keys[4]) {
			$(".lamp:gt(2), .lamp:eq(1)").removeClass("lamp-off").addClass("lamp-on");
		} else if(keys[4] && !keys[3]) {
			$(".lamp:eq(0), .lamp:eq(2), .lamp:gt(2)").removeClass("lamp-off").addClass("lamp-on");
			//liga lamp 4 e 5(brilho forte), demais fraco
		} else if(keys[3] && keys[4]) {
			$(".lamp").removeClass("lamp-off").addClass("lamp-on");
			//ligar tudo, 4 a 6 fraco, 1 a 3 forte
		}	
	}		
	
}

$(document).ready(function() {
	/* Conversao de energia luminosa em energia elétrica */	
	$("#btnConversao").click(function() {
		$.post("conversao/mswConversao.php");
	});
	
	$("#btnTemp").click(function() {
		calor();				
	});
	
	$("#btnAero").click(function() {
		aero();
	});
	
	/*  Código do experimento de Condução em barras */
	
	$("#btnlConducao").click(function() {
		$.post("conducao/mswConducao.php?c=l1");
		$("#condStatus").text("Ligado");
		
	});
	
	$("#btndConducao").click(function() {
		$.post("conducao/mswConducao.php?c=d1");
		$("#condStatus").text("Desligado");
		
	});
	
	if($("#condStatus").length == 1)
	{
		$(window).load(function() {
			$.post("conducao/mswConducao.php?c=d1");
			sleep(850);
		});
		$(window).unload(function() {
			$.post("conducao/mswConducao.php?c=d1");
			sleep(850);
		});
	}	
	
	
	
	/* Aerogerador */
	if($("#fieldAero").length == 1) {
		tempAero();
	}
	
	if($("#fieldCalor").length == 1) {
		calor();

		$(window).unload(function() {
			$.post("calor/mswCalor.php", { inTemp : "0" } );
			sleep(850);
		});
	}
	
	hold = false;
	$(".cmarm").click(function() {
		var cm = $(this).attr('id').substr(3);
		
		if(hold == false) {
			hold = true;
			$.post("arm/mswArm.php", {cm : cm}).complete(function() { hold = false; });
		} else {
			alert("Aguarde");
		}
	});	
		
	if($("#quadro").length == 1) {
		$("<img />").attr("src", "/quadro/images/shape8.png");
		$("<img />").attr("src", "/quadro/images/off.png");
		$("<img />").attr("src", "/quadro/images/quadror.jpg");
		desliga(5);
		
		$(window).unload(function() {
			desliga(5);
			sleep(850);
		});
		
		
		$(".chaves").click(function() {
			var key = $(this).attr("id").substr(6, 1);
			
			if($(this).hasClass("chave-on")) {
				$(this).prev().toggleClass("hide");
				$(this).toggleClass("hide");
				$.post("quadro/mswQuadro.php?c=d"+key);
			} else {
				$(this).next().toggleClass("hide");
				$(this).toggleClass("hide");
				
				$.post("/quadro/mswQuadro.php?c=l"+key);
			}					
			
			checkKeys();
		});								
	}
});

$(window).load(function() {
	if($("#CSViewer1").length > 0 &&  $.browser.msie) {
		start();
		$(window).unload(function() {
			leave();
		});
	}
});

