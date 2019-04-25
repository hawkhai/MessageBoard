<?php
$vcode = @file_get_contents('vcode.txt');

$input = @$_POST['vcode'];
$username = @$_POST['username'];
$password = @$_POST['password'];

if($input === $vcode && $username && $password){
	include('sql.php');
	$register = new Mysql();
	$register->username = $username;
	$register->password = $password;
	
	$register->Insert();
	
	if($register->pdo->errorCode() === '00000')
	{
		echo 'register success! please <a href="login.html">login</a>';
	}
	else
	{
		die('username has already existed! please <a href="register.html"> register again! </a>');
	}
	
}
elseif ($input !== $vcode) {
	echo 'vcode error!';
	echo "
		<a href='register.html'>register again</a>
	";
}

else{
	echo 'username or password is null!';
	echo "
		<a href='register.html'>register again</a>
	";
}