准备通过写一个留言板来练习PHP，最近打比赛实在是感觉自己啥都不会，还是基础不行；一叶飘零大佬说了，入门web最好的方法还是先从写网站开始，了解运作流程；只顾一味地刷题，物极必反.

实现的功能：

- 可以注册、登录
- 可以发表留言，且留言的时候要填写验证码
- 留言的时候会显示用户名、发表时间
- 留言板每页有十条记录
- admin可以发表留言、删除留言、删除用户

要进行的设计：

- 前端设计
	- [留言板页面布局](#留言板页面布局)
- 后端设计
	- [php对mysql的操作](#php对mysql的操作)
	- [验证码设计](#验证码设计)
	- [cookie与session](#cookie与session)
	- [数据库管理](#数据库管理)

环境：

- windows10
- phpstudy2018，PHP-5.5.38

# [php对mysql的操作](#php对mysql的操作)

- [选择数据库api](#选择数据库api)
- [连接数据库](#连接数据库)
- [操作数据库](#操作数据库)


## [选择数据库api](#选择数据库api)

php连接mysql的三种方式：

- mysql api : 不便于扩展，如果迁移了不同类型的数据库需要重写脚本，且php5.5.0之后就废弃了

- mysqli api : mysql增强版扩展；mysqli扩展允许我们访问MySQL 4.1及以上版本提供的功能

- pdo

这个小项目用pdo来操作数据库，官方说明：

<blockquote>PHP 数据对象 （PDO） 扩展为PHP访问数据库定义了一个轻量级的一致接口。实现 PDO 接口的每个数据库驱动可以公开具体数据库的特性作为标准扩展功能。 注意利用 PDO 扩展自身并不能实现任何数据库功能；必须使用一个 具体数据库的 PDO 驱动 来访问数据库服务。 
<br><br>
PDO 提供了一个 数据访问 抽象层，这意味着，不管使用哪种数据库，都可以用相同的函数（方法）来查询和获取数据。 PDO 不提供 数据库 抽象层；它不会重写 SQL，也不会模拟缺失的特性。如果需要的话，应该使用一个成熟的抽象层。 
<br><br>
从 PHP 5.1 开始附带了 PDO，在 PHP 5.0 中是作为一个 PECL 扩展使用。 PDO 需要PHP 5 核心的新 OO 特性，因此不能在较早版本的 PHP 上运行。 </blockquote>

可以在phpinfo()中找到pdo的相关信息：

![](https://i.imgur.com/UIm2AmF.png)

## [连接数据库](#连接数据库)

以前我都是用mysql进行练习数据库的使用，于是把每次进去的用户名和密码给省去了，但在项目中用户名和密码可不能省略

打开`phpStudy\PHPTutorial\MySQL\my.ini`，去掉`skip-grant-tables`这个字段(加上后不需要用户名密码即可连接数据库)，mysql服务重启后，重置密码为`123456`，再次进入就要输入用户名密码了：

	mysql -uroot -p123456

首先，我需要创建一个数据库：

	create database MessageBoard;

之后，创建了一个`index.php`文件，写入：

``` php
<?php
$username = 'root';
$password = '123456';

#数据源名称DSN，包含了请求连接到数据库的信息
$dsn = 'mysql:dbname=MessageBoard;host=127.0.0.1';

#尝试连接，如果连接失败则抛出异常
try{
	#创建一个表示数据库连接的PDO实例
	$pdo = new PDO($dsn, $username, $password);
} catch(PEOException $e){
	echo $e->getMessage();
}

?>
```

## [操作数据库](#操作数据库)
使用创建好的pdo对象来实现对数据表的增、删、改、查;

实现很简单，根据mysql语句，调用pdo对象的方法即可：

- exec: 执行一条 SQL 语句，并返回受影响的行数 (执行无结果集的语句)
- query: 返回一个 PDOStatement object，遍历这个对象可以获取数据 (执行有结果集的语句)

``` php
<?php
$username = 'root';
$password = '123456';

//数据源名称，包含了请求连接到数据库的信息
$dsn = 'mysql:dbname=MessageBoard;host=127.0.0.1';

try{
	$pdo = new PDO($dsn, $username, $password);
} catch(PEOException $e){
	echo '数据库连接失败！';
}

/*创建表
$sql = 'create table hello(
	    id int unsigned not null auto_increment,
	    email varchar(20) not null,
	    age tinyint unsigned not null,
	    primary key(id)
    	);';
*/

# 查询数据
$sql = 'select * from hello';


/*删除表
$sql = 'drop table hello';
*/

/*添加数据
$sql = "insert into hello(id, email, age) values (1,'123456789@qq.com', 18)";
*/

/*修改数据
$sql = "update hello set email='123@qq.com', age=20 where id=1";
*/

/*删除数据
$sql = "delete from hello where id=1";
*/

$res = $pdo->query($sql);

foreach ($res as $row) {
	echo $row['id']."<br>";
	echo $row['email']."<br>";
	echo $row['age'];
}

/*
$res = $pdo->exec($sql);
var_dump($res);
*/
?>
```

# [验证码设计](#验证码设计)

- [基本使用](#基本使用)
- [验证码的基本实现](#验证码的基本实现)


## [基本使用](#基本使用)

php中的GD库可以创建和处理图像

``` php
<?php

//创建画布,参数为画布大小
$img = imagecreatetruecolor(400, 400);

//设置颜色,第一个参数为画布，后面三个参数为三原色red、green、blue，用0-255表示深度
$white = imagecolorallocate($img, 255, 255, 255);
$black = imagecolorallocate($img, 0, 0, 0);
$red = imagecolorallocate($img, 255, 0, 0);
$green = imagecolorallocate($img, 0, 255, 0);
$blue = imagecolorallocate($img, 0, 0, 255);


//更改图像背景，图像背景默认为黑色
imagefill($img, 0, 0, $white);

/*画一条线，第二个参数到第四个参数为起点x轴、起点y轴、终点x轴、终点y轴
第一个参数为画布，第四个参数为颜色*/
imageline($img, 0, 0, 400, 400, $red);

//告诉浏览器如何解析图像
header('content-type:image/png');

//输出png图像
imagepng($img);

//释放资源
imagedestroy($img);

```

运行显示：

![](https://i.imgur.com/nL4NXXA.png)

## [验证码的基本实现](#验证码的基本实现)

还未学到cookie和session的设计，先用文件操作来进行验证码的验证

- file_get_contents():将整个文件读入一个字符串
- file_put_contents(filename， data):将一个字符串写入文件，文件不存在则新建文件，若存在则默认覆盖原文件内容

文件结构：

	WWW\MessageBoard
		-login.php
		-vcode.php
		-vcode.txt
		-vertify.php

其中，vcode.txt可以自动生成，且里面的内容为当前页面的验证码


login.php

	<!DOCTYPE html>
	
	<html>
	<head>
		<meta charset="utf-8">
		<title>验证码</title>
	</head>
	<body>
		<form action="./vertify.php", method="post">
			<input type="text" name="username" placeholder='username'><br>
			<input type="password" name="password" placeholder='password'><br>
			<input type="text" name="vcode" placeholder='验证码'>
			<img src="./vcode.php"><br>
			<p style="margin-left: 50px;"><input type="submit" name="submit"></p>
			
		</form>
	</body>
	</html>

vcode.php

``` php
<?php


class Vcode
{
	
	public function outimage()
	{
		$str = rand(1000, 9999);
		file_put_contents('vcode.txt', $str);
		//创建画布
		$img = imagecreatetruecolor(40, 20);

		//设置颜色
		$white = imagecolorallocate($img, 255, 255, 255);
		$black = imagecolorallocate($img, 0, 0, 0);

		//填充背景为黑色
		imagefill($img, 0, 0, $black);

		//画字符串
		imagestring($img, 5, 0, 3, $str, $white);

		//输出图像
		header('content-type:image/png;charset="utf-8"');
		imagepng($img);

		//销毁资源
		imagedestroy($img);


	}
}

$vcode=new Vcode();
$vcode->outimage();
```

vertify.php

``` php
<?php

$vcode = file_get_contents('vcode.txt');

$input = $_POST['vcode'];

if($input === $vcode){
	echo 'right';
}
else{
	echo 'wrong';
}
```

效果：

![](https://i.imgur.com/AS2asLv.png)

# [cookie与session](#cookie与session)

## 无状态HTTP

HTTP请求头：
- Content-Type: 服务器发送的内容的MIME类型
- Cookie：设置的cookie值


HTTP响应头：
- Content-Disposition: 指示客户端下载文件
- Set-Cookie: 第一次访问服务器端，服务器端返回的cookie

不同浏览器存储cookie位置是不一样的

## 设置cookie

cookie.php   
``` php
<?php
/*第一个参数为cookie名字，第二个参数为cookie的值
time()为当前时间戳，加3600代表cookie有效期为从现在起一小时*/

setcookie('username', 'admin', time()+3600);

```

现在访问这个页面，之后在浏览器设置中可以看到cookie的详细信息：

![](https://i.imgur.com/Sedl8wv.png)

而且可以在network中看到响应头：

    Set-Cookie: username=admin; expires=Mon, 22-Apr-2019 15:50:42 GMT; Max-Age=3600

可以看到返回的cookie的名字和数据，以及失效时间(expires)

第一次请求时在请求头中并不会看到cookie这个字段，但刷新页面，会发现请求头中多出来了：

	Cookie: username=admin



# [数据库管理](#数据库管理)

用数据库需要存储用户的：

- 用户名
- 留言时间
- 留言内容

