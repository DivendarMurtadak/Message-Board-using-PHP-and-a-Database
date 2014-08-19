<!DOCTYPE HTML>
<html>
<head>
<title>Message Board</title>
<style>
.error {
	color: #FF0000;
}
</style>
</head>
<body> 

<?php
session_start ();
// define variables and set to empty values
$message = $messageErr = "";
$login_session = $_SESSION ['login_user'];
if (! isset ( $login_session )) {
	header ( "Location: board.php" );
}

if (isset ( $_POST ['logout'] )) {
	
	unset ( $_SESSION );
	session_destroy ();
	session_write_close ();
	header ( "Location: board.php?message=logout" );
	die ();
}

if (isset ( $_POST ['submit'] )) {
	
	if (empty ( $_POST ["message"] )) {
		$messageErr = "Please enter a message";
	} else {
		$message = test_input ( $_POST ["message"] );
		
		try {
			$dbname = dirname ( $_SERVER ["SCRIPT_FILENAME"] ) . "/mydb.sqlite";
			$dbh = new PDO ( "sqlite:$dbname" );
			$dbh->beginTransaction ();
			$uid = uniqid ( "", true );
			$query = "insert into posts values('$uid','$login_session', datetime('now','localtime'),'$message')";
			$dbh->exec ( $query ) or die ( print_r ( $dbh->errorInfo (), true ) );
			$dbh->commit ();
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
try {
	$dbname = dirname ( $_SERVER ["SCRIPT_FILENAME"] ) . "/mydb.sqlite";
	$dbh = new PDO ( "sqlite:$dbname" );
	$dbh->beginTransaction ();
	
	$stmt = $dbh->prepare ( "select * from posts, users where posts.postedby=users.username" );
	$stmt->execute ();
	
	echo "<h2>Welcome " . $login_session . "</h2><h3>Below are the message history of all users</h3>";
	print "<pre><table width='70%' border='1' align='center'><tr>  <th>Username</th>  <th>Fullname</th>  <th>Date Posted</th>  <th>Message</th>  </tr><tr>";
	while ( $row = $stmt->fetch () ) {
		echo "<tr><td>" . $row ['username'] . "</td><td>" . $row ['fullname'] . "</td><td>" . $row ['datetime'] . "</td><td>" . $row ['message'] . "</tr>";
	}
	print "</table></pre>";
} catch ( PDOException $e ) {
	print "Error!: " . $e->getMessage () . "<br/>";
	die ();
}
?>

<form method="post">
		<table width="300" border="0" align="center" cellpadding="0"
			cellspacing="1" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="3" cellspacing="1"
						bgcolor="#FFFFFF">
						<tr>
							<span class="error"><?php echo $messageErr;?></span>
							<td colspan="3"><strong></strong></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>

							<td><input type="submit" name="logout" value="logout"></td>
						</tr>
						<tr>
							<td><h3>Message</h3></td>
							<td>:</td>
							<td><input type="text" name="message" id="message"></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><input type="submit" name="submit" value="Submit"></td>
						</tr>
					</table>
				</td>
			</tr>
			</form>

</body>
</html>
