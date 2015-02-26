<?php
/* TODO: Trabalhar com heranças */
class expMSW {
	private $_config = array(
						"quadro" => "10.10.10.22",
						"conversao" => "10.10.10.9",
						"calor" => "10.10.10.13",
						"arm" => "10.10.10.21",
						"conducao" => "10.10.10.23",
						"aero" => "10.10.10.30"
						);
	
	
	protected function mswSend($ip, $command, $ret=false) {
		$fp = fsockopen($ip, 80, $errno, $errstr, 15);

		if(!fp) {
			trigger_error("Não foi possivel conectar ao MSW ".$ip, E_USER_WARNING);
		} else {
			$out =  "GET /" . $command ." HTTP/1.1\r\n";
			$out .= "Host: " . $ip . ":80/". $command . "\r\n";
			$out .= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);
			
			if($ret) {
				$ret = "";
				
				while(!feof($fp)) {
					$ret .= fgets($fp, 128);
				}
			}
			
			fclose($fp);
			return $ret;
		}
	}
	
	public function ligaConducao($chave, $tempo=120) {
		$this->workBackground();		
		$this->mswSend($this->_config["conducao"], "liga" . $chave . ".cgi");
		sleep($tempo);
		$this->desligaConversao();
		$this->mswSend($this->_config["conducao"], "desliga" . $chave . ".cgi");
	}
	
	public function desligaConducao($chave) {
		if($chave == 5) {
			for($i=1; $i<=4; $i++) {
				$this->mswSend($this->_config["conducao"], "desliga" . $i . ".cgi");			
			}
		} else {
			$this->mswSend($this->_config["conducao"], "desliga" . $chave . ".cgi");
		}
	}
	
	
	public function ligaQuadro($chave) {
		$this->mswSend($this->_config["quadro"], "liga" . $chave . ".cgi");
	}
	
	public function desligaQuadro($chave) {
		if($chave == 5) {
			for($i=1; $i<=4; $i++) {
				$this->mswSend($this->_config["quadro"], "desliga" . $i . ".cgi");			
			}
		} else {
			$this->mswSend($this->_config["quadro"], "desliga" . $chave . ".cgi");
		}
	}
	
	public function ligaConversao() {
		$this->mswSend($this->_config["conversao"], "?4=1");
	}
	
	public function desligaConversao() {
		$this->mswSend($this->_config["conversao"], "?4=0");
	}
	
	public function setAero($yel=0, $red=0) {
		if($red <= 55 && $red >= $yel) 
			$this->mswSend($this->_config["aero"], "temperatura.html?x=".$yel."&y=".$red);
	}
	
	public function getStatsAero() {
		$curl = curl_init($this->_config["aero"]."/teste.xml");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$ret = curl_exec($curl);
		curl_close($curl);
		
		return $ret;
//		return $this->mswSend($this->_config["aero"], "teste.xml", true);
		
	}
	
	protected function workBackground() {		
	//	ob_start();
		ignore_user_abort(true);
		header("Connection: close");
		header("Content-length: 0");
//		ob_end_flush();
		flush();
	}
	
	public function timerConversao($tempo=30) {	
		$this->workBackground();		
		$this->ligaConversao();
		sleep($tempo);
		$this->desligaConversao();
	}
	
	public function getTemp() {
		return $this->mswSend($this->_config["calor"] , "temperature.cgi", true);
	}
					
	public function setTemp($temp) {
		if($temp < 0) {
			$temp = 0;
		} elseif($temp > 99) {
			$temp = 99;
		}
		
		$this->mswSend($this->_config["calor"], "body.htm?s=".$temp);						
	}

	
		
	
						
}

class roboticArm extends expMSW {
	const START = 0;
	const GET = 1;
	const LOAD = 2;
	const UNLOAD = 3;
	const PUT = 4;
	
	
	/*
	array(x => array(posMin, posMax), y => array(posMin, posMax), ...);
	x , y, ... servo channels */
	private $_posLimits = array(0 => array(500, 2450), 1 => array(700, 2260), 2 => array(810, 2120), 3 => array(500, 2500), 4 => array(1450, 1870 /*aberto*/));
	
	
	private $_commands = array("start" => array("1P1723S750", "2P1743S750", "4P908S750", "3P917S750", "0P1500S750"),
								"quadro" => array("2P1286S750", "0P1500S750", "1P1053S750", "3P772S750", "4P831S750"),
								"estrela" => array("2P1073S750", "0P1694S750", "1P947S750", "3P597S750", "4P831S750"),
								"circulo" => array("2P1267S750", "0P1908S750", "1P1044S750", "3P743S750", "4P831S750"),
								"triangulo" => array("2P1277S750", "0P2083S750", "1P1024S750","3P787S750",  "4P831S750"),
								
								"carregarQ" => array("1P1442S750", "0P917S750", "2P1102S750", "3P772S750", "1P1102S750", "4P908S750"/*, "1P1442S750"*/),
								"carregarE" => array("1p1442S750", "0P1015S750", "2P1073s750", "3P743S750", "1P1083S750", "4P908S750"/*, "1p1442S750"*/),
								"carregarC" => array("1p1442S750", "0P1131S750", "2P1024s750", "3P850S750", "1P995S750", "4P908S750"/*, "1p1442S750"*/),
								"carregarT" => array("1p1442S750", "0P840S750", "2P1277s750", "3P1024S750", "1P1209S750", "4P908S750"/*, "1p1442S750"*/),
								
								"prepareStart" => array("1P2100", "2P2100", "3P1100", "0P1500"),
								"prepareOff" => array("1P2100S750", "2P2100S750", "3P1100S750", "0P1500S750"),
								"off" => array("0L", "1L", "2L", "3L", "4L")
								
								
								);
								
								
	public function __construct() {
		$this->initialPos(false);
	}
	
	public function __destruct() {
		$this->initialPos();		
		$this->setLowAll();		
	}
	
	public function initialPos($opt = 750) {
		$this->setServoPos(1, 2100, $opt);
		$this->setServoPos(3, 1100, $opt);
		$this->setServoPos(2, 2100, $opt);		
		$this->setServoPos(0, 1500, $opt);
		$this->setServoPos(4, 1870, $opt);
	}
	
	public function movement($fig) {
		switch($fig) {
			case 'square':				
				//pegar da base
				$this->setServoPos(0, 1267);
				$this->setServoPos(2, 1218);
				$this->setServoPos(3, 597);
				$this->setServoPos(1, 1005);				
				$this->setServoPos(4, 1578);

				
				//Levantar
				$this->setServoPos(1, 1277);
				
				//Soltar
				$this->setServoPos(0, 927);
				$this->setServoPos(2, 1218);								
				$this->setServoPos(3, 597);
				$this->setServoPos(1, 1005);		
				$this->setServoPos(4, 1704);				
				break;
				
			case 'star8':
				//pegar da base
				$this->setServoPos(0, 1840);
				$this->setServoPos(2, 1791);
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1160);				
				$this->setServoPos(4, 1597);

				
				//Levantar
				$this->setServoPos(1, 1383);
				
				//Soltar
				$this->setServoPos(0, 840);
				$this->setServoPos(2, 1791);								
				$this->setServoPos(3, 1423);
				$this->setServoPos(1, 1178);		
				$this->setServoPos(4, 1743);	
				break;
				
			case 'hexagon':
				//pegar
				$this->setServoPos(0, 1413);
				$this->setServoPos(2, 1955);
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1354);				
				$this->setServoPos(4, 1636);
				
				//Levantar
				$this->setServoPos(1, 1500);
				
				//Soltar
				$this->setServoPos(0, 995);
				$this->setServoPos(2, 1955);								
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1354);		
				$this->setServoPos(4, 1782);					
				break;

			case 'triangle':
				//pegar
				$this->setServoPos(0, 1568);
				$this->setServoPos(2, 1752);
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1150);			
				$this->setServoPos(4, 1636);
				
				//Levantar
				$this->setServoPos(1, 1277);
				
				//Soltar
				$this->setServoPos(0, 1025);
				$this->setServoPos(2, 1752);								
				$this->setServoPos(3, 1363);
				$this->setServoPos(1, 1227);		
				$this->setServoPos(4, 1744);					
				break;	
				
			case 'circle':
				//pegar
				$this->setServoPos(0, 2141);
				$this->setServoPos(2, 1752);
				$this->setServoPos(3, 1665);
				$this->setServoPos(1, 1073);			
				$this->setServoPos(4, 1636);
				
				//Levantar
				$this->setServoPos(1, 1400);
				
				//Soltar
				$this->setServoPos(0, 714);
				$this->setServoPos(2, 1772);								
				$this->setServoPos(3, 1568);
				$this->setServoPos(1, 1169);		
				$this->setServoPos(4, 1796);				
				break;	
				
			case 'star5':
				//pegar
				$this->setServoPos(0, 1995);
				$this->setServoPos(2, 1888);
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1248);			
				$this->setServoPos(4, 1636);
				
				//Levantar
				$this->setServoPos(1, 1430);
				
				//Soltar
				$this->setServoPos(0, 752);
				$this->setServoPos(2, 1908);								
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1257);		
				$this->setServoPos(4, 1801);				
				break;

			case 'cross':
				//pegar
				$this->setServoPos(0, 1674);
				$this->setServoPos(2, 2092);
				$this->setServoPos(3, 1684);
				$this->setServoPos(1, 1331);			
				$this->setServoPos(4, 1713);
				
				//Levantar
				$this->setServoPos(1, 2000);
				
				//Soltar
				$this->setServoPos(0, 762);
				$this->setServoPos(2, 2092);								
				$this->setServoPos(3, 1684);
				$this->setServoPos(1, 1345);		
				$this->setServoPos(4, 1782);	
				break;		
				
				
###################### Molde para base #####################
			case 'rsquare':
				//pegar do molde
				$this->setServoPos(0, 927);
				$this->setServoPos(2, 1218);
				$this->setServoPos(3, 597);
				$this->setServoPos(1, 1005);				
				$this->setServoPos(4, 1578);

				
				//Levantar
				$this->setServoPos(1, 1400);
				
				//Soltar
				$this->setServoPos(0, 1267);
				$this->setServoPos(2, 1218);								
				$this->setServoPos(3, 597);
				$this->setServoPos(1, 995);		
				$this->setServoPos(4, 1704);				
				break;
				
			case 'rstar8':
				//pegar do molde
				$this->setServoPos(0, 840);
				$this->setServoPos(2, 1791);
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1150);				
				$this->setServoPos(4, 1627);

				
				//Levantar
				$this->setServoPos(1, 1383);
				
				//Soltar
				$this->setServoPos(0, 1840);
				$this->setServoPos(2, 1791);								
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1180);		
				$this->setServoPos(4, 1743);	
				break;
				
			case 'rhexagon':
				//pegar do molde
				$this->setServoPos(0, 995);
				$this->setServoPos(2, 1995);
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1354);				
				$this->setServoPos(4, 1636);
				
				//Levantar
				$this->setServoPos(1, 1500);
				
				//Soltar
				$this->setServoPos(0, 1413);
				$this->setServoPos(2, 1995);								
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1354);		
				$this->setServoPos(4, 1782);					
				break;

			case 'rtriangle':
				//pegar do molde
				$this->setServoPos(0, 1034);
				$this->setServoPos(2, 1752);
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1140);			
				$this->setServoPos(4, 1636);
				
				//Levantar
				$this->setServoPos(1, 1277);
				
				//Soltar
				$this->setServoPos(0, 1568);
				$this->setServoPos(2, 1752);								
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1170);		
				$this->setServoPos(4, 1734);					
				break;	
				
			case 'rcircle':
				//pegar do molde
				$this->setServoPos(0, 704);
				$this->setServoPos(2, 1752);
				$this->setServoPos(3, 1665);
				$this->setServoPos(1, 1063);			
				$this->setServoPos(4, 1636);
				
				//Levantar
				$this->setServoPos(1, 1400);
				
				//Soltar
				$this->setServoPos(0, 2131);
				$this->setServoPos(2, 1752);								
				$this->setServoPos(3, 1665);
				$this->setServoPos(1, 1092);		
				$this->setServoPos(4, 1782);				
				break;	
				
			case 'rstar5':
				//pegar do molde
				$this->setServoPos(0, 752);
				$this->setServoPos(2, 1908);
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1238);			
				$this->setServoPos(4, 1636);
				
				//Levantar
				$this->setServoPos(1, 1430);
				
				//Soltar
				$this->setServoPos(0, 1995);
				$this->setServoPos(2, 1888);								
				$this->setServoPos(3, 1500);
				$this->setServoPos(1, 1248);		
				$this->setServoPos(4, 1859);				
				break;

			case 'rcross':
				//pegar do molde
				$this->setServoPos(0, 752);
				$this->setServoPos(2, 2092);
				$this->setServoPos(3, 1684);
				$this->setServoPos(1, 1335);			
				$this->setServoPos(4, 1636);
				
				//Levantar
				$this->setServoPos(1, 2000);
				
				//Soltar
				$this->setServoPos(0, 1674);
				$this->setServoPos(2, 2092);								
				$this->setServoPos(3, 1684);
				$this->setServoPos(1, 1351);		
				$this->setServoPos(4, 1870);	
				break;																					
		}
	
		
	}
	
	private function servoExists($servo) {
		if(array_key_exists($servo, $this->_posLimits))
			return true;
		else 
			throw new Exception('Servo "'.$servo.'" not found.');
	}
	
	public function setLow($servo) {
		if($this->servoExists($servo))
			$this->sendCmm($servo."L");
	}
	
	public function setLowAll() {
		foreach($this->_posLimits as $key => $value) {
			$this->setLow($key);
		}
	}
								
	public function setServoPos($servo, $pos, $speed=750) {
		if($this->servoExists($servo)) {
			if($pos >= $this->_posLimits[$servo][0] && $pos <= $this->_posLimits[$servo][1]) {
				$this->sendCmm($servo."P".$pos.  ( ($speed) ? "S".$speed : '' ) );
			} else {
				throw new Exception('The position for servo "'.$servo.'" must be between '.$this->_posLimits[$servo][0].' and '.$this->_posLimits[$servo][1].'
				but '.$pos.' set');
			}
		}		
	}
			
								
	protected function sendCmm($cm) {
		$this->mswSend("10.10.10.21", "?C=#".$cm);
	}
	
/*	public function start() {
		foreach($this->_commands["start"] as $cm) {	
			$this->sendCmm($cm);
		}
	}
	
	public function test($opt) {
		foreach($this->_commands[$opt] as $cm) {
			$this->sendCmm($cm);
		}
	}
	
	public function setLow() {
		$this->test("prepareOff");
		$this->test("off");
	}*/
								
	
}
?>