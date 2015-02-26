<?php

/**
 * Description of newPHPClass
 *
 * @author rexlab
 * 
 */
require_once 'Conexao.php';

class DaoConnection {
   public static $instance;
   //public static $mutex;
   
   public function __construct(){       
      
     /* if (!isset(self::$mutex)){
          $this->mutex = new SyncMutex();
      }*/
    }
    
    public static function getInstance() {
        if (!isset(self::$instance)){ 
            self::$instance = new DaoConnection();
        }
        return self::$instance;
    }

    
    
    public function queue() {
        $sql = "SELECT MAX(position) FROM queue";
        //$this->mutex->lock();
        try{
            $result =  Conexao::getInstance()->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $id_result = $row["MAX(position)"];   
            $count = (is_null($id_result) ? 0 : $id_result + 1); 
            $sql2 = "INSERT INTO queue (position,id,arrivalTime) VALUES (:position,:id,:arrivalTime)";
            $p_sql = Conexao::getInstance()->prepare($sql2);
            $p_sql->bindValue(":position", $count);
            $p_sql->bindValue(":id", $_SESSION['id']);
            $p_sql->bindValue(":arrivalTime", $_SESSION['arriving_time']);
            $retorno = $p_sql->execute(); 
          //  $this->mutex->unlock();
            return $retorno;
        } catch (Exception $e) {
            //$this->mutex->unlock();
			return 0;     
        }
    }
     
    public function delete($id) {
       if (is_null($id)) {
            $id = $this->dequeue();
        }
       //$this->mutex->lock();
       $sql = "DELETE FROM queue WHERE id = :id";
       try {
           $p_sql = Conexao::getInstance()->prepare($sql); 
           $p_sql->bindValue(':id', $id); 
           $retorno = $p_sql->execute(); 
         //  $this->mutex->unlock();
           return $retorno;
       } catch (Exception $e) {
           // escreve log
           //$this->mutex->unlock();
           return "Ocorreu um erro ao tentar excluir o usuario, contate o administrador ou tente novamente mais tarde."; 
       }

       

    }
    
    public function dequeue() {
        $sql = "SELECT id FROM  queue WHERE position = (SELECT MIN(position) FROM queue)";
        //$this->mutex->lock();
        try{
            $result =  Conexao::getInstance()->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            //$this->mutex->unlock();
            return $row["id"];      
        }catch(Exception $e){
            //$this->mutex->unlock();
            return 'Ocorreu um erro ao tentar entrar na fila, contate o administrador ou tente mais tarde';
        } 

    }
    
    public function firstRunningTime(){        
        $sql = "SELECT startingTime FROM  queue WHERE position = (select MIN(position) from queue)";
        //$this->mutex->lock();
        try{
            $result =  Conexao::getInstance()->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);
          //$this->mutex->unlock();
            return $row["startingTime"];   
            
        }catch(Exception $e){
          //  $this->mutex->unlock();
            return 0;
        }  
        
    }
    
    public function setTimeBegin($id){
       $sql = "UPDATE queue SET startingTime=:time WHERE id =:id";

      // $this->mutex->lock();
       try {
           $p_sql = Conexao::getInstance()->prepare($sql); 
           $p_sql->bindValue(':id', $id); 
           $p_sql->bindValue(':time', time()); 
           $retorno = $p_sql->execute();
        //   $this->mutex->unlock();
           return $retorno;
       } catch (Exception $e) {
           // escreve log
          // $this->mutex->unlock();
          return "Ocorreu um erro ao tentar setar o tempo de inicio da experiência, contate o administrador ou tente novamente mais tarde."; 
       }
       
    }
    
    public function getLastTimeAlive($id){        
        $sql = "SELECT lastTimeAlive FROM queue WHERE id = :id";
       // $this->mutex->lock();
        try {
            $p_sql = Conexao::getInstance()->prepare($sql); 
            $p_sql->bindValue(':id', $id);
            $p_sql->execute(); 
            $row = $p_sql->fetch(PDO::FETCH_ASSOC);
         //   $this->mutex->unlock();
            return $row["lastTimeAlive"];   
            
        }catch(Exception $e){
           // $this->mutex->unlock();
            return 0;
        }  
    }
    
    public function setLastTimeAlive($id){
       $sql = "UPDATE queue SET lastTimeAlive= :time WHERE id = :id ";
       //$this->mutex->lock();
       try {
           $p_sql = Conexao::getInstance()->prepare($sql); 
           $p_sql->bindValue(':id', $id); 
           $p_sql->bindValue(':time', time()); 
           $retorno = $p_sql->execute(); 
         //  $this->mutex->unlock();
           return $retorno;
       } catch (Exception $e) {
           // escreve log
           //$this->mutex->unlock();
           return "Ocorreu um erro ao tentar setar o ultimo tempo de refresh, contate o administrador ou tente novamente mais tarde."; 
       }
    }


    public function waiting($id) {

       $sql = "SELECT count(position) FROM `queue` WHERE position > (SELECT MIN(position) FROM queue) "
               . "and position < (SELECT MIN(position) FROM queue WHERE id =:id)";
       //$this->mutex->lock();
       try {
           $p_sql = Conexao::getInstance()->prepare($sql); 
           $p_sql->bindValue(':id', $id); 
           $p_sql->execute(); 
           $row = $p_sql->fetch(PDO::FETCH_ASSOC);
         //  $this->mutex->unlock();
           return $row['count(position)'];   
       } catch (Exception $e) { 
           // escreve log
           //$this->mutex->unlock();
           return "Ocorreu um erro ao tentar consultar o numero de usuarios na fila, contate o administrador ou tente novamente mais tarde."; 
       }
    }
    

    public function __destruct(){
         
    }
    
  
}
    

