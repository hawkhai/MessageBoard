<?php

$vcode = @file_get_contents('vcode.txt');

$input = @$_POST['vcode'];
$username = @$_POST['username'];
$password = @$_POST['password'];

if ($input === $vcode && $username && $password) {
    include('sql.php');
    $login = new Mysql();
    $login->username = $username;
    $login->password = $password;

    if ($login->Query()) {
        echo 'welcome ' . $username;
    } else {
        die('username or password is wrong! please <a href="login.html"> login again! </a>');
    }
} elseif ($input !== $vcode) {
    echo 'vcode error!';
    echo "
		<a href='login.html'>login again</a>
	";
} else {
    echo 'username or password is null!';
    echo "
		<a href='login.html'>login again</a>
	";
}