<?php

  class Category {
   
    private $conn;
    private $table = 'categories';
    public $id;
    public $category;

    public function __construct() {
      $this->conn = $GLOBALS['db'];
    }

    public function read() {

			$result = null;

			try {

				$query = 'SELECT id, category FROM ' . $this->table . ' ORDER BY id';
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

				$query = 'SELECT id, category FROM ' . $this->table . ' WHERE id = ?';
				$stmt = $this->conn->prepare($query);
				$stmt->bindParam(1, $id);

				$stmt->execute();

				$result = $stmt;

			} catch (Exception $e) {

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}

			return $result;
		}

		public function getId($category) {

			$result = null;

			try	{

				$query = 'SELECT id FROM ' . $this->table . ' WHERE category = ?';
				$stmt = $this->conn->prepare($query);
				$stmt->bindParam(1, $category);

				$stmt->execute();

				$result = $stmt->fetchColumn();

			} catch (Exception $e) {

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}

			return $result;
		}

		public function create() {

			$result = null;
			$existingId = $this->getId($this->category);

			if ($existingId) {

				// if the query to check whether category is already in the database fails, pass along the error message. Otherwise, return an error message that category already exists.
				return $existingId['status'] && $existingId['status'] == 'error' ? $existingId : array('status'=>'error', 'message'=>"Cateogry $this->category already exists with an id of $existingId."); 
			}

			try {
				$query = 'INSERT INTO ' . $this->table . ' (category) VALUES (:category)' ;
				$stmt = $this->conn->prepare($query);
				$stmt-> bindParam(':category', $this->category);

				if($stmt->execute()) {

					$id = $this->getId($this->category);

					$this->id = $id;

					$result = array('status'=>'success');
				}

			} catch (Exception $e) {

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}

			return $result;
		}

		public function exists($id) {

			$result = null;

			try {

				$queryResult = $this->read_single($id);
				$rows = $queryResult->fetchAll();

				return array('status'=>'success', 'result'=>count($rows) > 0);

			} catch (Exception $e) {

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}
			
			return $result;
		}

		public function isBeingUsedInQuotes ($category_id) {

			$result = null;

			try {

				$query = 'SELECT * FROM quotes WHERE category_id = :id';
				$stmt = $this->conn->prepare($query);
				$stmt->bindParam(':id', $category_id);
				
				$stmt->execute();
				
				$count = $stmt->rowCount();

				$result = array('status'=>'success', 'result'=>$count > 0);

			} catch (Exception $e) {

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}
			
			return $result;
		}

		public function update() {

			$itemExists = $this->exists($this->id);

			if ($itemExists['status'] && $itemExists['status'] == 'error') {

				return $itemExists;

			} else if (!$itemExists['result']) {

				return array('status'=>'error', 'message'=>'category_id Not Found');
			}

			$result = null;

			try {

				$query = 'UPDATE ' . $this->table . ' SET category = :category WHERE id = :id';
				$stmt = $this->conn->prepare($query);
				$stmt-> bindParam(':category', $this->category);
				$stmt-> bindParam(':id', $this->id);

				$stmt->execute();

				$result = array('status'=>'success');

			} catch (Exception $e) {

				$result = array('status'=>'error', 'message'=>$e->getMessage());
			}	

			return $result;
		}

		public function delete($id) {

			$result = null;
			$this->id = htmlspecialchars(strip_tags($id));
			$entryExists = $this->exists($this->id);
			$isReferenced = $this->isBeingUsedInQuotes($this->id);

			if ($entryExists['status'] && $entryExists['status'] == 'error') {

				return $entryExists;

			} else if (!$entryExists['result']) {

				return array('status'=>'error', 'message'=>'category_id Not Found');
			}

			if ($isReferenced['status'] && $isReferenced['status'] == 'error') {

				return  $isReferenced;

			} else if ($isReferenced['result'] == true) {

				return array('status'=>'error', 'message'=>'Cannot delete category because it is referenced in another table.');
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