
<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8">
	<title>login</title> 
	
</head>
<body>
	<h1 style="text-align: center;">Welcome to login MessageBoard!</h1>
	<form action="./login.php" method="post" style="text-align: center;" autocomplete="off">
		<input type="text" name="username" placeholder='username' maxlength="16"><br>
		<input type="password" name="password" placeholder='password' maxlength="16"><br>
		<input type="text" name="vcode" placeholder='验证码' style="margin-left: 45px;"> 
		<img src="./vcode.php"><br>
		<input type='submit' value="submit">
		<button type='submit' formaction="./register.php">register</button>
	</form>
</body>
</html>


<?php
session_start();

$vcode = file_get_contents('vcode.txt');

$input = @$_POST['vcode'];
$username = @$_POST['username'];
$password = @$_POST['password'];

if($input === $vcode && $username && $password)
{
	include('sql.php');
	$login = new Mysql();
	$login->table = 'users';
	$login->username = $username;
	$login->password = $password;

	if($login->Login())
	{
		$_SESSION['username'] = $username;
		
		header('location: index.php');
	}
	
	else
	{
		die('<p style="text-align: center">username or password is wrong! please login again! </p>');
	}

}

elseif ($input !== $vcode) {
	echo '<p style="text-align: center">vcode error! please login again!</p>';
}

else{
	echo '<p style="text-align: center">username or password is null!  please login again!</p>';
}

?>
