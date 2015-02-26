<?php

session_start();


function output_header($title){
	echo <<<EOZ
		<!DOCTYPE html>

		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
			<meta name="viewport" content="width=device-width, initial-scale=1"/>	
		<link rel='stylesheet' type='text/css' href='./style/bootstrap.min.css'/>
 		<link rel='stylesheet' type='text/css' href='./style/style.css'/>
 		<link rel='stylesheet' type='text/css' href='./style/experiment.css'/>
 		
		<script type='text/javascript' src='./scripts/jquery.min.js'></script>		
		<script type='text/javascript' src='./scripts/bootstrap.min.js'></script>
		<script type='text/javascript' src='./scripts/script.js'></script>	
EOZ;
	echo <<<EOZ
		<title>$title</title>
	</head>
	<body>
EOZ;
}

function output_footer(){
	echo <<<EOZ
	<div class="container desk">
		<footer>
			<div style="padding-bottom:9px;margin:40px 0 20px;border-bottom:1px solid #eee"> </div>
			<p class="pull-right">
				<a href="https://twitter.com/RExLab_UFSC" class="twitter-follow-button" data-show-count="false">Follow @RExLab_UFSC</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>  
				<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FrexlabARA&amp;width&amp;layout=button&amp;action=like&amp;show_faces=true&amp;share=true&amp;height=80&amp;appId=286075631498830" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:20px;" allowTransparency="true"></iframe>
							
				<a href="http://ufsc.br"><img src="./images/ufsc.png" height="50"/></a> 
				<a href="#"><img src="./images/r.png" height="40" /></a> </p>
		  	</footer>
	  	</div>
	</body>
</html>
EOZ;
}

function output_menu($ativo){
	$vec=array("inicio"=>"", "sobre"=>"", "equipe"=>"", "projetos"=>"", "experimentos"=>"",
					"publicacoes"=>"", "parceiros"=>"", "contato"=>"",);

	$vec[$ativo] = " class='active'";
	
	$eng=array("inicio"=>"en/index", "sobre"=>"en/about", "equipe"=>"en/people", "projetos"=>"en/projects", "experimentos"=>"../en/experiments/index",
					"publicacoes"=>"en/publications", "en/parceiros"=>"cooperation", "contato"=>"en/contact");
	echo <<<EOZ
<div class="navbar-wrapper">
  <div class="container">
    <div class="navbar navbar-inverse navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          	<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> 
          	<span class="icon-bar"></span> <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="../index.php"><img src="./images/logo.png" /></a> </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li {$vec['inicio']}><a href="../index.php">Início</a></li>
            <li {$vec['sobre']}><a href="../sobre.php">Sobre</a></li>
            <li {$vec['equipe']}><a href="../equipe.php">Equipe</a></li>
			<li {$vec['projetos']}><a href="../projetos.php">Projetos</a></li>
            <li {$vec['experimentos']}><a href="index.php">Experimentos</a></li>
            <li {$vec['publicacoes']}><a href="../publicacoes.php">Publicações</a></li>
            <li {$vec['parceiros']}><a href="../parceiros.php">Parceiros</a></li>
            <li {$vec['contato']}><a href="../contato.php">Contato</a></li>
			<li><a href="http://rexlab.ufsc.br/blog">Blog</a></li>			
            </li>
          </ul>
		  <ul class="desk nav navbar-nav pull-right">
				<li class="desk" style="margin-right:-20px;"><a href="#"><img src="./images/pt.png"/></a></li>
				<li class="desk" style="margin-right:20px;"><a href="{$eng[$ativo]}.php"><img src="./images/en.png"/></a></a></li>
			</ul>
        </div>
      </div>
    </div>
  </div>
</div>
	
EOZ;

}


function detecta_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== 0))
        return true;
    else
        return false;
}

?>
