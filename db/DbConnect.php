<?php 

	require_once "../config/config.php";

	class DbConnect {

		public function connect() {
			$connectionString = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";";

			try {
				$conn =  new PDO($connectionString, DB_USER, DB_PASSWORD);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return $conn;
			} catch( PDOException $e) {
				echo 'Database Error: ' . $e->getMessage();
			}
		}
	}
 ?>