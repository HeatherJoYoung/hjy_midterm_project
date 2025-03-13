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
			require_once(__DIR__ . '/Category.php');
			require_once(__DIR__ . '/Author.php');
    }

    public function read($filters, $random) {

			$result = null;

			try {

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

				$result = $stmt;

			} catch (Exception $e) {
				
				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}
      
			return $result;
    }

  public function read_single($id) {

		$result = null;

		try {

			$query = 'SELECT q.id, q.quote, a.author, c.category FROM ' . $this->table . ' q 
			JOIN categories c ON q.category_id = c.id
			JOIN authors a ON q.author_id = a.id
			WHERE q.id = ?';

			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(1, $id);

			$stmt->execute();

			$result = $stmt;

		} catch (Exception $e) {

			$result = array('status'=>'error', 'message'=>$e->getMessage());
		}

    return $result;
  }

  public function getId() {

		$result = null;

		try {

			$query = 'SELECT id FROM ' . $this->table . ' WHERE quote = ?';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(1, $this->quote);

			$stmt->execute();

			$result = $stmt->fetchColumn();

		} catch (Exception $e) {

			$result = array('status'=>'error', 'message'=>$e->getMessage());
		}

    return $result;
  }

  public function create() {

		$result = null;

    $this->quote = htmlspecialchars(strip_tags($this->quote));
    $this->author_id = htmlspecialchars(strip_tags($this->author_id));
    $this->category_id = htmlspecialchars(strip_tags($this->category_id));

    $existingId = $this->getId();

    if ($existingId) {
      
			// if the query to check whether quote is already in the database fails, pass along the error message. Otherwise, return error that quote already exists.
			return $existingId['status'] && $existingId['status'] == 'error' ? $existingId : array('status'=>'error', 'message'=>"This quote already exists with an id of $existingId."); 
    }

    $findCategory = (new Category())->exists($this->category_id);
		$categoryExists = $findCategory['status'] && $findCategory['status'] == 'success' ? $findCategory['result'] : false;

    $findAuthor = (new Author())->exists($this->author_id);
		$authorExists = $findAuthor['status'] && $findAuthor['status'] == 'success' ? $findAuthor['result'] : false;

    if (!$categoryExists) {
      return array('status'=>'error', 'message'=>'category_id Not Found');
    }

		if ($categoryExists['status'] && $categoryExists['status'] == 'error') {
			return $categoryExists;
		}

    if (!$authorExists) {
      return array('status'=>'error', 'message'=>'author_id Not Found');
    }

		if ($authorExists['status'] && $authorExists['status'] == 'error') {
			return $authorExists;
		}

		try {

			$query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)' ;

			$stmt = $this->conn->prepare($query);

			$stmt-> bindParam(':quote', $this->quote);
			$stmt-> bindParam(':author_id', $this->author_id);
			$stmt-> bindParam(':category_id', $this->category_id);

			$stmt->execute();

			$id = $this->getId();

			$this->id = $id;
			$this->quote = htmlspecialchars_decode($this->quote);

			$result = array('status'=>'success');
		} catch (Exception $e) {

			$result = array('status'=>'error', 'message'=>$e->getMessage());
		}

    return $result;
  }

  private function exists($id) {

		$result = null;

		try {

			$queryResult = $this->read_single($id);
			$rows = $queryResult->fetchAll();

			$result = array('status'=>'success', 'result'=>count($rows) > 0);

		} catch (Exception $e) {

			$result = array('status'=>'error', 'message'=>$e->getMessage());
		}

    return $result;
  }

  public function update() {

		$result = null;

		$itemExists = $this->exists($this->id);

		if ($itemExists['status'] && $itemExists['status'] == 'error') {

			return $itemExists;

		} else  if (!$itemExists['result']) {

			return array('status'=>'error', 'message'=>'No Quotes Found');
		}

		$findCategory = (new Category())->exists($this->category_id);
		$categoryExists = $findCategory['status'] && $findCategory['status'] == 'success' ? $findCategory['result'] : false;

    $findAuthor = (new Author())->exists($this->author_id);
		$authorExists = $findAuthor['status'] && $findAuthor['status'] == 'success' ? $findAuthor['result'] : false;

    if (!$categoryExists) {
      return array('status'=>'error', 'message'=>'category_id Not Found');
    }

		if ($findCategory['status'] && $findCategory['status'] == 'error') {
			return $findCategory;
		}

    if (!$authorExists) {
      return array('status'=>'error', 'message'=>'author_id Not Found');
    }

		if ($findAuthor['status'] && $findAuthor['status'] == 'error') {
			return $findAuthor;
		}

		try {
			$this->quote = htmlspecialchars(strip_tags($this->quote));
			$this->id = htmlspecialchars(strip_tags($this->id));

			$query = 'UPDATE ' . $this->table . ' SET quote = :quote, category_id = :category_id, author_id = :author_id WHERE id = :id';
			$stmt = $this->conn->prepare($query);

			$stmt-> bindParam(':quote', $this->quote);
			$stmt-> bindParam(':category_id', $this->category_id);
			$stmt-> bindParam(':author_id', $this->author_id);
			$stmt-> bindParam(':id', $this->id);

			$stmt->execute();

			$this->quote = htmlspecialchars_decode($this->quote);
			$result = array('status'=>'success');

		} catch (Exception $e) {

			$result = array('status'=>'error', 'message'=>$e->getMessage());	
		}
    
    return $result;
  }

  public function delete($id) {

		$result = null;
		$this->id = htmlspecialchars(strip_tags($id));
		$itemExists = $this->exists($id);

		if ($itemExists['status'] && $itemExists['status'] == 'error') {

			return $itemExists;
			
		} else if (!$itemExists['result']) {

			return array('status'=>'error', 'message'=>'No Quotes Found');
		}

		try {

			$query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
			$stmt = $this->conn->prepare($query);
			$stmt-> bindParam(':id', $this->id);

			$stmt->execute();

			$result = array('status'=>'success');

		} catch (Exception $e) {

			$result = array('status'=>'error', 'message'=>$e->getMessage());
		}
    
    return $result;
  }
}