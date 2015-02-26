/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var str_url = "http://"+window.location.hostname +"/exp/queue/handle.php";       


function LeaveExperiment(){

var dataout = {funcao:0, pass:$("#pass").html()};

calor_close();   

$.ajax({
        url: str_url ,
        data:dataout,
        type:"POST",
        timeout:5000,
        success: function(data){$( "#divExp" ).html("<p>Experiência finalizada com "+data+ "</p>");
                            $("#btnLeave").off("click", LeaveExperiment);
							}     
        });

 
}
 

 
$(document).ready(function(){
    $("#btnLeave").click(LeaveExperiment);  
});
