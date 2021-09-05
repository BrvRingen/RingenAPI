<?php
namespace Api\Database;

class DatabaseConnection
{
	public function Connect()
	{
		$conn = mysqli_connect("localhost", "INPUT_USER_HERE", "INPUT_PASSWORD_OF_USER_HERE", "INPUT_DATABASE_HERE");
		$conn->set_charset("utf8");
		return $conn;
	}
	
	public function Close($conn)
	{
		$conn -> close();
	}
}