<?php

  class Author {
   
    private $conn;
    private $table = 'authors';
    public $id;
    public $name;

    public function __construct() {

      $this->conn = $GLOBALS['db'];
    }

    public function read() {

      $query = 'SELECT id, author FROM ' . $this->table . ' ORDER BY id';

      $stmt = $this->conn->prepare($query);

      $stmt->execute();

      return $stmt;
    }

    public function read_single($id) {

      $query = 'SELECT id, author FROM ' . $this->table . ' WHERE id = ?';

      $stmt = $this->conn->prepare($query);

      $stmt->bindParam(1, $id);

      $stmt->execute();

      return $stmt;
    }

    public function getId($name) {

      $query = 'SELECT id FROM ' . $this->table . ' WHERE author = ?';

      $stmt = $this->conn->prepare($query);

      $stmt->bindParam(1, $name);

      $stmt->execute();

      $result = $stmt->fetchColumn();

      return $result;
    }

    public function create() {

      $existingId = $this->getId($this->name);

      if ($existingId) {
        return array('status'=>'error', 'message'=>"Author $this->name already exists with an id of $existingId.");
      }

      $query = 'INSERT INTO ' . $this->table . ' (author) VALUES (:name)' ;

      $stmt = $this->conn->prepare($query);

      $this->name = htmlspecialchars(strip_tags($this->name));

      $stmt-> bindParam(':name', $this->name);

      if($stmt->execute()) {

        $id = $this->getId($this->name);

        $this->id = $id;

        return array('status'=>'success');
      }

      return array('status'=>'error', 'message'=>$stmt->error);
    }

    public function exists($id) {

      $queryResult = $this->read_single($id);
      $rows = $queryResult->fetchAll();

      return count($rows) > 0;
    }

    public function update() {

      if (!$this->exists($this->id)) {
        return array('status'=>'error', 'message'=>'author_id Not Found');
      }

      $query = 'UPDATE ' . $this->table . ' SET author = :name WHERE id = :id';

      $stmt = $this->conn->prepare($query);

      $this->name = htmlspecialchars(strip_tags($this->name));
      $this->id = htmlspecialchars(strip_tags($this->id));
      $stmt-> bindParam(':name', $this->name);
      $stmt-> bindParam(':id', $this->id);

      if($stmt->execute()) {

        return array("status"=>"success");
      }

      return array("status"=>"error", "message"=>"update failed");
    }

    public function delete($id) {

      if (!$this->exists($id)) {
        return array('status'=>'error', 'message'=>'author_id Not Found');
      }

      $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

      $stmt = $this->conn->prepare($query);

      $this->id = htmlspecialchars(strip_tags($id));

      $stmt-> bindParam(':id', $this->id);

      if ($stmt->execute()) {

        return array("status"=>"success");
      }
      
      return array("status"=>"error", "message"=>"author_id Not Found");
    }
  }