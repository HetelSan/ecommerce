<?php 

namespace Hcode\DB;

class Sql {

	const HOSTNAME = "127.0.0.1";
	const USERNAME = "root";
	// const PASSWORD = "root";
	const PASSWORD = "";
	const DBNAME = "db_ecommerce";

	private $conn;

        /**
         * 
         */
	public function __construct()
	{

		$this->conn = new \PDO(
			"mysql:dbname=".Sql::DBNAME.";host=".Sql::HOSTNAME, 
			Sql::USERNAME,
			Sql::PASSWORD
		);

	}

	/**
         * 
         * @param type $statement
         * @param type $parameters
         */
        private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	/**
         * 
         * @param type $statement
         * @param type $key
         * @param type $value
         */
        private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}

	/**
         * 
         * @param type $rawQuery
         * @param type $params
         */
        public function query($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

	}

	/**
         * 
         * @param type $rawQuery
         * @param type $params
         * @return array
         */
        public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

 ?>