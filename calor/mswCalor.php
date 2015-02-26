<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
include("../expMSW.php");
require_once('../queue/DaoConnection.php');
session_start();

$atual = DaoConnection::getInstance()->dequeue();
if(strcmp($atual,session_id())==0){

$mswCalor = new expMSW();

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		$temp = $_POST["inTemp"];
		
		//if(!empty($temp)) {
			$temp = intval($temp);	
		//}
	}

	$mswCalor->setTemp($temp);	

	$nowTemp = $mswCalor->getTemp();

	$json['temp'] = $nowTemp;

	if($nowTemp >= $temp) {
		$json['status'] = 'Desligada';
	} else {
		$json['status'] = 'Ligada';
	}

	echo json_encode($json);


			
}else{
	echo 'Access Denied';
			
}

?>
