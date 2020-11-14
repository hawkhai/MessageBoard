<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <title>MessageBoard</title>
    </head>
    <body>
        <?php
        session_start();

        if (isset($_SESSION['username'])) {
            echo "
	<h2 style='text-align: center;'>
	<form action='./logout.php', method='get'>
		" . $_SESSION['username'] . " <input type='submit' value='logout'>
	</form>
	Welcome to MessageBoard!
	</h2>
	";
        } else {
            echo '<h1 style="text-align: center;">Please <a href="login.php">login</a> first!</h1>';
        }


        include('sql.php');
        $exe = new Mysql();
        $exe->table = 'message';
        $exe->Select();
        echo '<p style="text-align: center;" ><textarea readonly="readonly" rows="18" cols="80" placeholder="暂时还没有留言." wrap="hard">';
        foreach ($exe->res as $row) {
            if ($row['content'] !== '') {
                echo $row['time'] . '  ';
                echo $row['username'];
                echo '
';
                echo $row['content'];
                echo '
';
                echo '-------------------------------------------------------------------------------';
                echo '
';
            }
        }


        $username = @$_SESSION['username'];

        $content = @$_POST['content'];

        if (isset($username) && isset($content)) {
            $exe->username = $username;
            $exe->content = $content;
            $exe->Leave_mess();
            header('location: ./index.php');
        }
        ?></textarea></p>

<br>
<form action='' id="messageform" method="post">
    <p style="text-align: center;" ><textarea rows="10" cols="80" name="content" from="messageform" placeholder="说些什么吧..."></textarea></p>
    <p style="text-align: center;" ><input type="submit" value="留言">
        <input type="reset" value="清空">
    </p>
</form>
</body>
</html>

