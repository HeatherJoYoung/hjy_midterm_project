<?php

  class Quote {
   
    private $conn;
    private $table = 'quotes';
    public $id;
    public $quote;
    public $category_id;
    public $author_id;

    public function __construct() {
      $this->conn = $GLOBALS['db'];
    }

    public function read($filters, $random) {
      $query = 'SELECT q.id, q.quote, a.author, c.category
      FROM ' . $this->table . ' q 
      JOIN categories c ON q.category_id = c.id
      JOIN authors a ON q.author_id = a.id';

      if ($filters) {

        $whereStatement = ' WHERE ';
        $keys = array_keys($filters);

        for ($i = 0; $i < count($keys); $i++) {

          if ($i === 0) {

            $whereStatement .= $keys[$i] . ' = ' . $filters[$keys[$i]];

          } else {  

            $whereStatement .= ' AND ' . $keys[$i] . ' = ' . $filters[$keys[$i]];
          }
        }
        
        $query .= $whereStatement;
      }

      $end = $random ? ' ORDER BY random() LIMIT 1' : ' ORDER BY id';
      $query .= $end;

      $stmt = $this->conn->prepare($query);

      $stmt->execute();

      return $stmt;
    }

  public function read_single($id) {

    $query = 'SELECT q.id, q.quote, a.author, c.category FROM ' . $this->table . ' q 
    JOIN categories c ON q.category_id = c.id
    JOIN authors a ON q.author_id = a.id
    WHERE q.id = ?';

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(1, $id);

    $stmt->execute();

    return $stmt;
  }

  public function getId() {

    $query = 'SELECT id FROM ' . $this->table . ' WHERE quote = ?';

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(1, $this->quote);

    $stmt->execute();

    $result = $stmt->fetchColumn();

    return $result;
  }

  public function create() {

    $this->quote = htmlspecialchars(strip_tags($this->quote));
    $this->author_id = htmlspecialchars(strip_tags($this->author_id));
    $this->category_id = htmlspecialchars(strip_tags($this->category_id));

    $existingId = $this->getId();

    if ($existingId) {
      return array('status'=>'error', 'message'=>"Quote already exists with an id of $existingId.");
    }

    $categoryExists = (new Category())->exists($this->category_id);
    $authorExists = (new Author())->exists($this->author_id);

    if (!$categoryExists) {
      return array('status'=>'error', 'message'=>'category_id Not Found');
    }

    if (!$authorExists) {
      return array('status'=>'error', 'message'=>'author_id Not Found');
    }

    $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)' ;

    $stmt = $this->conn->prepare($query);

    $stmt-> bindParam(':quote', $this->quote);
    $stmt-> bindParam(':author_id', $this->author_id);
    $stmt-> bindParam(':category_id', $this->category_id);

    if($stmt->execute()) {

      $id = $this->getId();

      $this->id = $id;
      $this->quote = htmlspecialchars_decode($this->quote);

      return array('status'=>'success');
    }

    return array('status'=>'error', 'message'=>$stmt->error);
  }

  private function exists($id) {

    $queryResult = $this->read_single($id);
    $rows = $queryResult->fetchAll();

    return count($rows) > 0;
  }

  public function update() {

    if (!$this->exists($this->id)) {
      return array('status'=>'error', 'message'=>'No Quotes Found');
    }

    $query = 'UPDATE ' . $this->table . ' SET quote = :quote, category_id = :category_id, author_id = :author_id WHERE id = :id';

    $stmt = $this->conn->prepare($query);

    $this->quote = htmlspecialchars(strip_tags($this->quote));

    $this->id = htmlspecialchars(strip_tags($this->id));

    $stmt-> bindParam(':quote', $this->quote);

    $stmt-> bindParam(':category_id', $this->category_id);

    $stmt-> bindParam(':author_id', $this->author_id);

    $stmt-> bindParam(':id', $this->id);

    if($stmt->execute()) {

      $this->quote = htmlspecialchars_decode($this->quote);
      return array('status'=>'success');
    }

    return array('status'=>'error', 'message'=>$stmt->error);
  }

  public function delete($id) {

    if (!$this->exists($id)) {
      return array('status'=>'error', 'message'=>'No Quotes Found');
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