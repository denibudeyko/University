<?php


class Database
{

  private static $instance = null;
  private $pdo, $query, $error = false, $results, $count;


  private function __construct()
  {
    try{
      $this->pdo = new PDO("mysql:host=". Config::get('mysql.host') .";dbname=". Config::get('mysql.dbname') .";", Config::get('mysql.username'), Config::get('mysql.password'));
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


  public function query($sql, $params=[]){
    $this->error = false;
    $this->query = $this->pdo->prepare($sql);

    if(count($params)){
      $i = 1;
      foreach($params as $param){

        $this->query->bindValue($i, $param);
        $i++;
      }
    }

    if(!$this->query->execute()){
      $this->error = true;
    }
    else{
      $this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
      $this->count = $this->query->rowCount();
    }
    return $this;

  }

  public function get($table, $where=[]){

    return $this->action('SELECT *', $table, $where);
  }

  public function delete($table, $where=[]){

    return $this->action('DELETE', $table, $where);

  }

  public function action($action, $table, $where){
    if(count($where) === 3){

      $operators = ['=', '>', '<', '<=', '>='];
      $field = $where[0];
      $operator = $where[1];
      $value = $where[2];

      if(in_array($operator, $operators)){

        $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
        if( !$this->query($sql, [$value])->showError() ){
          return $this;
        }
      }

    }
  }


  function insert( $table, $fields =[] )
    {

      $keys = array_keys($fields);

      $values = '';
      foreach($fields as $field){

        $values .= '?,';

      }
      $val = rtrim($values, ',');


      $sql = "INSERT INTO ${table} (" . '`'  .implode('`,`', $keys) . '`' . ")  VALUES ({$val})";

      if(!$this->query($sql, $fields)->showError()){
        return true;
      }

      return false;
    }

  // SQL = UPDATE PRODUCTS SET username=?,password=?  WHERE id=4

  public function update($table, $id, $fields=[]){

    $set = '';
    foreach ($fields as $field=>$values){

      $set .= "{$field} = ?,";

    }
    $set = rtrim($set, ',');


    $sql = "UPDATE {$table} set {$set} WHERE  id=${id}";

    if(!$this->query($sql, $fields)->showError()){
      return true;
    }

    return false;

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
  
  public function first(){
  	
  	return $this->showResult()[0];
	}

}