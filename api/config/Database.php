<?php 
  class Database {
    
    private $host;
		private $port;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
      $this->host = getenv('HOST');
			$this->port = 5432;
      $this->db_name = getenv('DBNAME');
      $this->username = getenv('USERNAME');
      $this->password = getenv('PASSWORD');
    }

    public function connect() {
      $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";
      if (is_null($this->conn)) {
        try { 
          $this->conn = new PDO($dsn, $this->username, $this->password);
          $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
          echo 'Connection Error: ' . $e->getMessage();
        }
      } 
      return $this->conn;    
    }
  }