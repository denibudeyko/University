<?php


class Database
{

  private static $instance = null;
  private $pdo, $query, $error = false, $results, $count;


  private function __construct()
  {
    try{
      $this->pdo = new PDO("mysql:host=localhost;dbname=university;", 'mysql', 'mysql');
    }
    catch (PDOException $e){
      echo $e->getMessage();
    }
  }

  public function getInstance(){


    if(!isset(self::$instance)){
      self::$instance = new Database();
    }
    return self::$instance;
  }


  public function query($sql){

    $this->error = false;

    $this->query = $this->pdo->prepare($sql);
    if( !$this->query->execute() ){
      $this->error = true;
    }
    else {
      $this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
      $this->count = $this->query->rowCount();
    }
    return $this;

  }

  public function showError()
  {
    return $this->error;
  }

  public function showResult(){
    return $this->results;
  }

  public function count(){


    return $this->count;

  }

}