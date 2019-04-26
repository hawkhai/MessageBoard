<?php

session_start();

$username = @$_POST['username'];
$password = @$_POST['password'];

if(isset($username) && isset($password))
{
	$_SESSION['username'] = $username;
}


if(isset($_SESSION['username'])){
	echo 'login success! welcome ' . $_SESSION['username'] . '<br>';

echo "
	<form action='./logout.php'>
	<input type='submit' value='logout'></input>
	</form>
";
}

else{
	echo 'please login first! <a href="login.php">login</a>';
}
