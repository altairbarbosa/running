<?php
try {
	$connection = new PDO("mysql: host=localhost; dbname=running; user=root; password=090697");
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}
