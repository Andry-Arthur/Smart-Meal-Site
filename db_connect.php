<?PHP
	$server = "cray.cs.gettysburg.edu";
	$dbase = "f23_3";
	$user = "rakoan02";
	$pass = "rakoan02";
	
	try {
		$db = new PDO("mysql:host=$server;dbname=$dbase",$user,$pass);
		//print "<H1>Successfully connected to the database.</H1>\n";
	}
	catch(PDOException $e) {
		die("Error connecting to the database " . $e->getMessage());
	}

?>
