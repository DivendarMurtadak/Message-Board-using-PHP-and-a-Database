<!--
Name: Divendar Murtadak
URL:http://omega.uta.edu/~dum4166/board.php

-->
<?php
session_start ();

if (isset ( $_POST ['new_user'] ))
	header ( "Location: newUserRegistration.php" );

if (isset ( $_POST ['login'] )) {
	
	// username and password sent from Form
	$myusername = addslashes ( $_POST ['myusername'] );
	$mypassword = addslashes ( $_POST ['mypassword'] );
	error_reporting ( E_ALL );
	ini_set ( 'display_errors', 'On' );
	$md5mypassword = md5 ( $mypassword );
	try {
		$dbname = dirname ( $_SERVER ["SCRIPT_FILENAME"] ) . "/mydb.sqlite";
		$dbh = new PDO ( "sqlite:$dbname" );
		$dbh->beginTransaction ();
		$stmt = $dbh->prepare ( "SELECT fullname FROM users WHERE username= '$myusername' and password= '$md5mypassword'" );
		$stmt->execute ();
		$rowCount = $stmt->fetchColumn ( 0 );
		if (empty ( $rowCount )) {
			$Error = "Your Login Name or Password is invalid";
		} else {
			session_register ( "myusername" );
			$_SESSION ['login_user'] = $myusername;
			
			header ( "location: userMessage.php" );
		}
	} catch ( PDOException $e ) {
		print "Error!: " . $e->getMessage () . "<br/>";
		die ();
	}
}

?>


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
if (! empty ( $_GET ['message'] )) {
	$msg = $_GET ['message'];
}
if (! empty ( $_GET ['user'] )) {
	$userExist = $_GET ['user'];
}

if (! empty ( $msg ) and $msg = "logout") {
	print "<h2 style='color:blue'>You $msg </h2>";
}
if (! empty ( $userExist )) {
	print "<h2 style='color:red'>UserName '$userExist' Already exist</h2>";
}
?>
<table width="300" height="100" border="0" align="center"
		cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
		<tr>
			<form name="form1" method="post">
				<td>
					<table width="100%" border="0" cellpadding="3" cellspacing="1"
						bgcolor="#FFFFFF">
						<tr>
							<span class="error"><?php echo $Error;?></span>
							<td colspan="3"><strong>Message Board Login</strong></td>
						</tr>
						<tr>
							<td width="78">Username</td>
							<td width="6">:</td>
							<td width="294"><input name="myusername" type="text"
								id="myusername"></td>
						</tr>
						<tr>
							<td>Password</td>
							<td>:</td>
							<td><input name="mypassword" type="password" id="mypassword"></td>
						</tr>
						<tr>
							<td><input type="submit" name="new_user"
								value="New users must register here">
							
							<td>&nbsp;</td>
							<td><input type="submit" name="login" value="Login"></td>
						</tr>
					</table>
				</td>
			</form>
		</tr>
	</table>
</body>
</html>