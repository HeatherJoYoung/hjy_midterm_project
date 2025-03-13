<?php

  class Author {
   
    private $conn;
    private $table = 'authors';
    public $id;
    public $name;

    public function __construct($db) {

      $this->conn = $db;
    }

    public function read() {

			$result = null;

			try {

				$query = 'SELECT id, author FROM ' . $this->table . ' ORDER BY id';
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

				$query = 'SELECT id, author FROM ' . $this->table . ' WHERE id = ?';
				$stmt = $this->conn->prepare($query);
				$stmt->bindParam(1, $id);

				$stmt->execute();

				$result = $stmt;

			} catch (Exception $e) {

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}
      
			return $result;
    }

    public function getId($name) {

			$result = null;

			try {
				$query = 'SELECT id FROM ' . $this->table . ' WHERE author = ?';
				$stmt = $this->conn->prepare($query);
				$stmt->bindParam(1, $name);
	
				$stmt->execute();
	
				$result = $stmt->fetchColumn();
	
			} catch (Exception $e) {

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}

			return $result;
    }

    public function create() {

			$result = null;

			$existingId = $this->getId($this->name);

			if ($existingId) {

				// if the query to check whether author is already in the database fails, pass along the error message. Otherwise, return error that author already exists.
				return $existingId['status'] && $existingId['status'] == 'error' ? $existingId : array('status'=>'error', 'message'=>"Author $this->name already exists with an id of $existingId."); 
			}

			try {

				$query = 'INSERT INTO ' . $this->table . ' (author) VALUES (:name)' ;
				$stmt = $this->conn->prepare($query);
				$stmt-> bindParam(':name', $this->name);

				$stmt->execute();

				$id = $this->getId($this->name);
				$this->id = $id;

				$result = array('status'=>'success');

			} catch (Exception $e) {	

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}

			return $result;
    }

    public function exists($id) {

			$result = null;

			try	{

				$queryResult = $this->read_single($id);
				$rows = $queryResult->fetchAll();

				return array('status'=>'success', 'result'=>count($rows) > 0);

			} catch (Exception $e) {

				return array('status'=>'error', 'message'=>$e->getMessage());
			}
    }

		public function isBeingUsedInQuotes ($author_id) {

			$result = null;
			
			try {

				$query = 'SELECT * FROM quotes WHERE author_id = :id';
				$stmt = $this->conn->prepare($query);
				$stmt->bindParam(':id', $author_id);
				
				$stmt->execute();
				
				$count = $stmt->rowCount();
				
				$result = array('status'=>'success', 'result'=>$count > 0);

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
			} else {
				if (!$itemExists['result']) {
					return array('status'=>'error', 'message'=>'author_id Not Found');
				}
			}
			
			try {
	
				$query = 'UPDATE ' . $this->table . ' SET author = :name WHERE id = :id';
				$stmt = $this->conn->prepare($query);
				$stmt-> bindParam(':name', $this->name);
				$stmt-> bindParam(':id', $this->id);
	
				$stmt->execute();
	
				$result = array("status"=>"success");

			} catch (Exception $e) {

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}

      return $result;
    }

    public function delete($id) {

			$result = null;
			$this->id = htmlspecialchars(strip_tags($id));
			$itemExists = $this->exists($this->id);
			$isReferenced = $this->isBeingUsedInQuotes($id);

			if ($itemExists['status'] && $itemExists['status'] == 'error') {

				return $itemExists;

			} else if (!$itemExists['result']) {

				return array('status'=>'error', 'message'=>'author_id Not Found');
			}

			if ($isReferenced['status'] && $isReferenced['status'] == 'error') {

				return  $isReferenced;

			} else if ($isReferenced['result']) {

				return array('status'=>'error', 'message'=>'Cannot delete author because it is referenced in another table.');
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