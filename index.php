<?php
    require_once('queue/DaoConnection.php');
	require_once('lib/lib.php');
    require_once('config.php');

	output_header("Experimentos");
    output_menu("experimentos");
	$_SESSION['id'] =session_id();
    $_SESSION['arriving_time']= time();
    if(DaoConnection::getInstance()->queue()){
		$atual = DaoConnection::getInstance()->dequeue();
		if(strcmp($atual,session_id())==0){
			DaoConnection::getInstance()->setTimeBegin(session_id());
			include('calor/calor.php');
			echo '<script src="'.$CFG->wwwroot.'/queue/access.js"></script>';

		}else{
		     include('calor/ncalor.php');
		     echo '<script src="'.$CFG->wwwroot.'/queue/waiting.js"></script>';
		}
		$n_waiting = DaoConnection::getInstance()->waiting(session_id());
        DaoConnection::getInstance()->setLastTimeAlive(session_id());           
        $firstime = DaoConnection::getInstance()->firstRunningTime();
        // funcao para formatar o tempo
        $time =   $n_waiting*60*1 + 60 - (time()-$firstime); 
        $message = "<p> <span id='info'>$n_waiting</span> usuarios estao esperando... </p>";
        $min = intval(($time/60));
        $seg = round((($time/60)-$min)*60);
        if($seg < 10){
            $seg = "0".$seg;
        }
        $message = $message."<p> Tempo  <span id='min' >$min</span>:<span id='seg' >$seg</span>s </p>";	
    }else{
        include('calor/ncalor.php');
		$message = 'O experimento já encontra-se aberto em uma aba, caso contrário clique em sair. Se o problema persistir, contate o administrador';
        echo '<script src="'.$CFG->wwwroot.'/queue/opennedtab.js"></script>';

	}

?>
<div class="alguma_classe">

<?php echo $message; ?>
    <button id="btnLeave" name="SAIR" >SAIR</button>
    <div style="display:none" id="pass"><?php echo md5(session_id());?></div>

</div>

<?php
	output_footer();
?>
