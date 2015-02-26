<?php
//include("./edimaxCam.php");
?>
<!-- Begin Experiment -->
<script src="./calor/calor.js"></script>

<div class="container">
	<center>
	<div class="page-header-exp">
		<div class="page-header">
			<h1>Meios de Propagação do Calor</h1>
		</div>
	</div>
  
  <div id="divExp" class="row">
    <div class="col-lg-2"> </div>
    <div class="col-lg-4">
		<img src="http://exp1:1234@rexlab.ararangua.ufsc.br:8109/mjpg/video.mjpg"/>
        <!-- ?=new edimaxCam(2);? -->
    </div>
        <div class="col-lg-4" id="panel">
		  <fieldset id="fieldCalor">
				<legend>Temperatura do Experimento</legend>
		        <p><span style="font-weight: bold">Temperatura atual:</span> <span id="temp">10</span>ºC</p> 
		        <p><span style="font-weight: bold">Lâmpada:</span> <span id="status">Desligada</span></p>
		        <p><span style="font-weight: bold">Temperatura desejada:</span>
	<input name="inTemp" type="text" id="inTemp" value="0" size="3" maxlength="2" />
		        ºC 
		        <input type="button" name="btnTemp" id="btnTemp" value="Ajustar" />
		        </p>
		  </fieldset>
        </div>	
	</div>
 </center>


	<!-- End Experiment --> 

<!-- Begin Explanation -->
<div style="margin-top:50px;"> </div>
<div class="container">
  <div class="row">
  <div class="col-md-1"></div>
    <div class="col-md-5"  style="padding:10px;; margin-left:10px; background-color: #eee; border: 1px solid #ddd;">
      <div class="row">
      	<div class="col-md-2"><img src="./images/oque.png"/></div>
        <div class="col-md-9"><p align="justify">Estudo dos meios de propagação do calor, demonstra as propagações por condução, convecção e irradiação, e compara o grau de isolamento térmico entre diferentes materiais. </div>
      </div>
    </div>
    <div class="col-md-5" style="padding:10px;; margin-left:10px; background-color: #eee; border: 1px solid #ddd;">
      <div class="row">
      <div class="col-md-2"><img src="./images/como.png"/></div>
        <div class="col-md-9"> <p align="justify"> Insira a temperatura desejada e clique em "Ajustar".  <a href="http://rexlab.ararangua.ufsc.br/moodle/course/view.php?id=2" target="_blank">Clique aqui para acessar o material didático.</a></div>
      </div>
    </div>
  </div>
</div>
<!-- End Explanation --> 

<!--
    <?php
	$browser_cliente = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if(strpos($browser_cliente, 'MSIE') == false)
	{
	 //  echo "<script language=\"javascript\" type=\"text/javascript\"> alert(\"Utilize o navegador Internet Explorer para melhor visualizar esta página. $variavel_1 \"); </script>";
	 //  echo "<script language=\"javascript\" type=\"text/javascript\"> //window.location ='http://rexlab.ararangua.ufsc.br/?q=experimentos' ;</script>";

	}?>
-->
