<?php

  class Category {
   
    private $conn;
    private $table = 'categories';
    public $id;
    public $name;

    public function __construct() {
      $this->conn = $GLOBALS['db'];
    }

    public function read() {
      $query = 'SELECT id, category FROM ' . $this->table . ' ORDER BY id';

      $stmt = $this->conn->prepare($query);

      $stmt->execute();

      return $stmt;
    }

  public function read_single($id) {

    $query = 'SELECT id, category FROM ' . $this->table . ' WHERE id = ?';

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(1, $id);

    $stmt->execute();

    return $stmt;
  }

  public function getId($name) {

    $query = 'SELECT id FROM ' . $this->table . ' WHERE category = ?';

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(1, $name);

    $stmt->execute();

    $result = $stmt->fetchColumn();

    return $result;
  }

  public function create() {

    $existingId = $this->getId($this->name);

    if ($existingId) {
      return array('status'=>'error', 'message'=>"Category $this->name already exists with an id of $existingId.");
    }

    $query = 'INSERT INTO ' . $this->table . ' (category) VALUES (:name)' ;

    $stmt = $this->conn->prepare($query);

    $this->name = htmlspecialchars(strip_tags($this->name));

    $stmt-> bindParam(':name', $this->name);

    if($stmt->execute()) {

      $id = $this->getId($this->name);

      $this->id = $id;

      return array('status'=>'success');
    }

    return array('status'=>'error', 'message'=>'failed to create new record');
  }

  public function exists($id) {

    $queryResult = $this->read_single($id);
    $rows = $queryResult->fetchAll();

    return count($rows) > 0;
  }

  public function update() {

    if (!$this->exists($this->id)) {
      return array('status'=>'error', 'message'=>'category_id Not Found');
    }

    $query = 'UPDATE ' . $this->table . ' SET category = :name WHERE id = :id';

    $stmt = $this->conn->prepare($query);

    $this->name = htmlspecialchars(strip_tags($this->name));

    $this->id = htmlspecialchars(strip_tags($this->id));

    $stmt-> bindParam(':name', $this->name);

    $stmt-> bindParam(':id', $this->id);

    if($stmt->execute()) {

      return array('status'=>'success');
    }

    return array('status'=>'error', 'message'=>$stmt->error);
  }

  public function delete($id) {

    if (!$this->exists($id)) {
      return array('status'=>'error', 'message'=>'category_id Not Found');
    }

    $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

    $stmt = $this->conn->prepare($query);

    $this->id = htmlspecialchars(strip_tags($id));

    $stmt-> bindParam(':id', $this->id);

    if ($stmt->execute()) {

      return array('status'=>'success');
    }
    
    return array('status'=>'error', 'message'=>$stmt->error);
  }
}