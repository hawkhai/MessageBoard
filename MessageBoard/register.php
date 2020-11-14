<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <title>register</title>
    </head>
    <body>
        <h1 style="text-align: center;">Welcome to register MessageBoard!</h1>
        <form action="./register.php" method="post" style="text-align: center;" autocomplete="off">
            <input type="text" name="username" placeholder='username' maxlength="16"><br>
            <input type="password" name="password" placeholder='password' maxlength="16"><br>
            <input type="text" name="vcode" placeholder='验证码' style="margin-left: 45px;"> 
            <img src="./vcode.php"><br>
            <input type='submit' value="submit">
            <button type='submit' formaction="./login.php">login</button>
        </form>
    </body>
</html>


<?php
$vcode = file_get_contents('vcode.txt');

$input = @$_POST['vcode'];
$username = @$_POST['username'];
$password = @$_POST['password'];

if ($input === $vcode && $username && $password) {
    include('sql.php');
    $register = new Mysql();
    $register->table = 'users';
    $register->username = $username;
    $register->password = $password;

    $register->Insert();

    if ($register->pdo->errorCode() === '00000') {
        header('location: login.php');
    } else {
        die('<p style="text-align: center">username has already existed! please register again!</p>');
    }
} elseif ($input !== $vcode) {
    echo '<p style="text-align: center">vcode error! please register again!</p>';
} else {

    echo '<p style="text-align: center">username or password is null! please register again!</p>';
}
?>

