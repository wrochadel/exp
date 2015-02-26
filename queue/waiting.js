/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var refresh, timer;
var str_url = "http://"+window.location.hostname +"/exp/queue/handle.php";

function setData(data){
    data = $.parseJSON(data);
    if(data["status"] > 0){
        $("#divExp").html(data["html"]);
        clearInterval(timer);
        $("#min").html(parseInt(data["time_exp"]/60));
        $("#seg").html(Math.round((data["time_exp"]/60-$("#min").html())*60));
        timer = setInterval(TimerMinus,1000);
        setTimeout(LeaveExperiment, data["time_exp"]*1000);
        refresh = setInterval(RefreshTimeAlive, 5000);   

    }else{
        var intval = parseInt(data['interval']);
        $( "#divExp" ).html("<p> Próxima requisicao em "+ intval +" segundos </p>");
        refresh = setInterval(Refresh, intval*1000);
    }
}   
    
function setDataRunning(data){
    data = $.parseJSON(data);   
}  

function Refresh () {  

var dataout = {funcao:1, pass:$("#pass").html()};
clearInterval(refresh);

$.ajax({
  url: str_url,
  data:dataout,
  type:"POST",
  timeout:60000,//1 minuto
  success: function(data){setData(data);}
});
}





function LeaveExperiment(){
  clearInterval(refresh);
  clearInterval (timer);
  clearInterval (calor_setinterval);

  var dataout = {funcao:0, pass:$("#pass").html()};

  calor_close();   
  
  $.ajax({
  url:str_url,
  data:dataout,
  type:"POST",
  timeout:5000,
  success: function(data){$( "#divExp" ).html("<p>ExperiÃªncia finalizada com "+ data+ "</p>");
                          $("#btnLeave").off("click", LeaveExperiment);}     
     });
  calor_close();


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

 refresh = setInterval(Refresh, 1000);   
 timer = setInterval(TimerMinus, 1000);
 $("#btnLeave").click(LeaveExperiment);
 $(window).on('beforeunload',function() {
    LeaveExperiment();
 });

});


