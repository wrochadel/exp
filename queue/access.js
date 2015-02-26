/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var str_url = "http://"+window.location.hostname +"/exp/queue/handle.php";       


function LeaveExperiment(){
	clearInterval(refresh);
	clearInterval (timer);
	clearInterval (calor_setinterval);

	$("#min").html('0');
	$("#seg").html('00');

	var dataout = {funcao:0, pass:$("#pass").html()};

	calor_close();   

	$.ajax({
			url: str_url ,
			data:dataout,
			type:"POST",
			timeout:5000,
			success: function(data){$( "#divExp" ).html("<p>Experiência finalizada com "+data+ "</p>");
								$("#btnLeave").off("click", LeaveExperiment);}     
			});

	 
}
 
    
function setDataRunning(data){
    data = $.parseJSON(data);   
} 

 function RefreshTimeAlive(){
  
  var dataout = {funcao:3, pass:$("#pass").html()};
  
  $.ajax({
  url: str_url,
  data:dataout,
  type:"POST",
  timeout:5000,
  success: function(data){setDataRunning(data);}     
     }); 
 }


 
function TimerMinus() { 
        var soma = parseInt($("#min").html())*60+ parseInt($("#seg").html());   
        soma =(soma-1)/60;
        
        if (soma > 0){
            $("#min").html(parseInt(soma));
            var seg = Math.round((soma - parseInt(soma))*60);
            var zero = "";
            if(seg <10){
               zero = "0";
            }
            $("#seg").html(zero+seg);
        }else{
            clearInterval (timer);
        }
}


 
$(document).ready(function(){
    $("#btnLeave").click(LeaveExperiment);
    $(window).on('beforeunload',function() {
        LeaveExperiment();
    });
    refresh = setInterval(RefreshTimeAlive, 5000);   
    setTimeout(LeaveExperiment, 60*1000);
    timer = setInterval(TimerMinus, 1000);
  
});
