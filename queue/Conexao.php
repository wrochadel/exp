<?php 

class Conexao { 
    public static $instance;
    
   
    
    private function __construct() {  }
    
    public static function getInstance() { 
        $servername = "localhost";
        $username = "root";
        $password = "rexlab";
        $dbname = "queue";
        
       if (!isset(self::$instance)) {
            self::$instance = new PDO('mysql:host='.$servername.';dbname='.$dbname, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING); 
            
        } 
        return self::$instance;
    } 
        
        
        }       

