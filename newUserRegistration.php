<!DOCTYPE HTML>
<html>
<head>
<style>
.error {
	color: #FF0000;
}
</style>
</head>
<body> 

<?php
$usernameErr = $emailErr = $passwordrErr = $fullnameErr = "";
$username = $password = $fullname = $email = "";

if ($_SERVER ["REQUEST_METHOD"] == "POST") {
	if (empty ( $_POST ["username"] )) {
		$usernameErr = "UserName is required";
	} else {
		$username = test_input ( $_POST ["username"] );
	}
	
	if (empty ( $_POST ["password"] )) {
		$passwordErr = "Password is required";
	} else {
		$password = test_input ( $_POST ["password"] );
	}
	
	if (empty ( $_POST ["fullname"] )) {
		$fullnameErr = "FullName is required";
	} else {
		$fullname = test_input ( $_POST ["fullname"] );
		// check if name only contains letters and whitespace
		if (! preg_match ( "/^[a-zA-Z ]*$/", $fullname )) {
			$fullnameErr = "Only letters and white space allowed";
		}
	}
	if (empty ( $_POST ["email"] )) {
		$emailErr = "Email is required";
	} else {
		$email = test_input ( $_POST ["email"] );
		// check if e-mail address syntax is valid
		if (! preg_match ( "/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email )) {
			$emailErr = "Invalid email format";
		}
	}
	if (empty ( $usernameErr ) and empty ( $passwordErr ) and empty ( $fullnameErr ) and empty ( $emailErr )) {
		$md5password = md5 ( $password );
		try {
			$dbname = dirname ( $_SERVER ["SCRIPT_FILENAME"] ) . "/mydb.sqlite";
			$dbh = new PDO ( "sqlite:$dbname" );
			$dbh->beginTransaction ();
			$stmt = $dbh->prepare ( "select * from users where username='$username'" );
			$stmt->execute ();
			$rowCount = $stmt->fetchColumn ( 0 );
			if (empty ( $rowCount )) {
				$dbh->exec ( "insert into users (username,password,fullname,email) values('$username', '$md5password', '$fullname', '$email')" ) or die ( print_r ( $dbh->errorInfo (), true ) );
				$dbh->commit ();
				header ( "Location:board.php" );
			} else {
				header ( 'Refresh:4; url=board.php?user=' . "$username" );
				print '<h2 style="color:#FF0000">UserName Already Exists<h2><h3 style="color:#FF0000">Login using same UserName<h3>';
			}
		} catch ( PDOException $e ) {
			print "Error!: " . $e->getMessage () . "<br/>";
			die ();
		}
	}
}
function test_input($data) {
	$data = trim ( $data );
	$data = stripslashes ( $data );
	$data = htmlspecialchars ( $data );
	return $data;
}
?>

<h2>New User Registration</h2>
	<p>
		<span class="error">* required field.</span>
	</p>
	<form method="post"
		action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		UserName: <input type="text" name="username"> <span class="error">* <?php echo $usernameErr;?></span>
		<br> <br> Password: <input type="password" name="password"> <span
			class="error">* <?php echo $passwordErr;?></span> <br> <br> Fullname:
		<input type="text" name="fullname"> <span class="error">* <?php echo $fullnameErr;?></span>
		<br> <br> Email: <input type="text" name="email"> <span class="error">* <?php echo $emailErr;?></span>
		<br> <br> <input type="submit" name="submit" value="Submit">
	</form>

</body>
</html>
