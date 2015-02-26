<?php 
$html = '
    <div class="col-lg-2"> </div>
    <div class="col-lg-4">
		<img src="http://exp1:1234@rexlab.ararangua.ufsc.br:8109/mjpg/video.mjpg"/>
        
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
        </div>';
	?>

<?php	
require_once 'DaoConnection.php';
session_start();
$id = session_id();
ini_set('max_execution_time', 60); // importante para espera de 30s
if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST['pass']) == md5($id)) {
    
    switch ($_POST['funcao']){
        case 0 : //decline or leave
            DaoConnection::getInstance()->delete($id);
            session_destroy();
            echo "sucesso"; 
        break;

        case 1 : //waiting
            $json['time_exp']=60;
            $firstime = DaoConnection::getInstance()->firstRunningTime();
            $waiting = DaoConnection::getInstance()->waiting($id);
            if($firstime > 0){  
                $json['time'] =  $json['time_exp'] - (time()-$firstime) + $waiting*$json['time_exp']; 
                $atual = DaoConnection::getInstance()->dequeue();
                $last_time =  time() - DaoConnection::getInstance()->getLastTimeAlive($atual);
                $json['last_time'] = $last_time;
                if($last_time > 20){
                    DaoConnection::getInstance()->delete($atual);
                    $json['status'] = 0; 
                    $json['interval'] = 1;      
                    
                }else if($json['time'] > 20){
                    if($waiting > 0){
                        $json['interval'] = intval($json['time']/(5*$waiting));
                    }else{
                        $json['interval'] = intval($json['time']/5);
                    }
                    
                }else{            
                    while($json['time'] > 0  && strcmp(DaoConnection::getInstance()->dequeue(),$id) !=0 ){
                        sleep(3); 
                        $json['time'] =$json['time'] -3;                  
                    }
                    $atual = DaoConnection::getInstance()->dequeue();
                    if(strcmp($atual,$id) !=0){
                        DaoConnection::getInstance()->delete($atual);
                    }
                    DaoConnection::getInstance()->setTimeBegin($id);
                    $json['html'] = $html;
                    $json['status'] = 1;              
                    
                }
                
            }else{
                $atual = DaoConnection::getInstance()->dequeue();
                if(strcmp($atual,$id) !=0){
                $last_time = time() - DaoConnection::getInstance()->getLastTimeAlive(DaoConnection::getInstance()->dequeue());
                    if($last_time > 20){
                        DaoConnection::getInstance()->delete($atual);
                    }         
                    $json['status'] = 0; 
                    $json['interval'] = 1;
                }else{
                    DaoConnection::getInstance()->setTimeBegin($id);
                    $json['html'] = $html;
                    $json['status'] = 1;              
                    }
            }     
            echo json_encode($json);
            DaoConnection::getInstance()->setLastTimeAlive($id);           
        break;
        
        
        case 3:
            DaoConnection::getInstance()->setLastTimeAlive($id);           
        break;
        
    }
  
}else{
    echo "Access Denied";
}
