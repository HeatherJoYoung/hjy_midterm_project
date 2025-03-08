<?php

  class Quote {
   
    private $conn;
    private $table = 'quotes';
    public $id;
    public $quotation;
    public $category_id;
    public $author_id;

    public function __construct() {
      $this->conn = $GLOBALS['db'];
    }

    public function read($filters) {
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

    $stmt->bindParam(1, $this->quotation);

    $stmt->execute();

    $result = $stmt->fetchColumn();

    return $result;
  }

  public function create() {

    $existingId = $this->getId();

    if (!$existingId) {

      $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) VALUES (:quotation, :author_id, :category_id)' ;

      $stmt = $this->conn->prepare($query);

      $this->quotation = htmlspecialchars(strip_tags($this->quotation));
      $this->author_id = htmlspecialchars(strip_tags($this->author_id));
      $this->category_id = htmlspecialchars(strip_tags($this->category_id));

      $stmt-> bindParam(':quotation', $this->quotation);
      $stmt-> bindParam(':author_id', $this->author_id);
      $stmt-> bindParam(':category_id', $this->category_id);

      if($stmt->execute()) {

        $id = $this->getId();

        $this->id = $id;

        return true;
      }

      printf("Error: %s.\n", $stmt->error);

      return false;

    } else {

      echo "Quote already exists with an id of $existingId.";
    }
  }

  public function update() {

    $query = 'UPDATE ' . $this->table . ' SET quote = :quotation, category_id = :category_id, author_id = :author_id WHERE id = :id';

    $stmt = $this->conn->prepare($query);

    $this->quotation = htmlspecialchars(strip_tags($this->quotation));

    $this->id = htmlspecialchars(strip_tags($this->id));

    $stmt-> bindParam(':quotation', $this->quotation);

    $stmt-> bindParam(':category_id', $this->category_id);

    $stmt-> bindParam(':author_id', $this->author_id);

    $stmt-> bindParam(':id', $this->id);

    if($stmt->execute()) {

      return true;
    }

    printf("Error: %s.\n", $stmt->error);

    return false;
  }

  public function delete($id) {

    $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

    $stmt = $this->conn->prepare($query);

    $this->id = htmlspecialchars(strip_tags($id));

    $stmt-> bindParam(':id', $this->id);

    if ($stmt->execute()) {

      return true;
    }

    printf("Error: %s.\n", $stmt->error);
    
    return false;
  }
}