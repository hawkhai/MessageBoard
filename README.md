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
	- [php操作数据库](#php操作数据库)
	- [验证码设计](#验证码设计)
	- [会话管理](#会话管理)
	- [数据库管理](#数据库管理)

环境：

- windows10
- phpstudy2018，PHP-5.5.38

---
2019.4.25

弄了几乎一天，用最近学的知识做了简单的实现注册登陆功能[login_and_register](login_and_register)

文件结构：

	-login.html 登陆页面，通向register.html和login.php
	-login.php 根据接收到的vcode、username与password判断是否登陆成功
	-register.html 注册页面，通向login.html和register.php
	-register.php 根据接收到的vcode、username与password判断是否注册成功
	-vcode.php 生成验证码
	-sql.php 封装好了一个用于php操作mysql的类



# [php操作数据库](#php操作数据库)

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

## [异常处理](#异常处理)

需要设置用户名为不可重复的；在注册的时候，使用的语句为`insert into`，如果username字段设置了unique，且注册时输入的用户名已存在，默认情况下是不会报错的，此时数据库也没有添加数据；

- errorCode(): 获取跟数据库句柄上一次操作相关的 SQLSTATE；如果没有任何错误, errorCode() 返回的是: '00000'

则可以用pdo对象调用这个方法，根据值是否是'00000'来判断sql语句执行情况(这里的五个0位string类型)

但执行query方法时，并不能用此方法看到是否执行成功，返回的错误码均为'00000'不知道原因是什么...

# [验证码设计](#验证码设计)

- [GD库的基本使用](#GD库的基本使用)
- [验证码的基本实现](#验证码的基本实现)


## [GD库的基本使用](#GD库的基本使用)

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

制作简单的验证码图片就是将随机数绘制到画布上；验证验证码时，可以将随机数写入到文件，之后取出与输入的验证码作对比，一致则验证成功

绘制验证码：
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

		//画字符串，第二个参数为字体大小
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



# [会话管理](#会话管理)

- [无状态HTTP](#无状态HTTP)
- [设置cookie](#设置cookie)
- [获取cookie](#获取cookie)
- [删除cookie](#删除cookie)
- [session](#session)
- [cookie与session，用哪个？](#cookie与session，用哪个？)


## [无状态HTTP](#无状态HTTP)

HTTP请求头：
- Content-Type: 发送的内容的MIME类型
- Cookie：设置的cookie值


HTTP响应头：
- Content-Disposition: 指示客户端下载文件
- Set-Cookie: 第一次访问服务器端，服务器端返回的cookie


## [设置cookie](#设置cookie)


``` php cookie.php   
<?php
/*第一个参数为cookie名字，第二个参数为cookie的值
time()为当前时间戳，加3600代表cookie有效期为从现在起一小时*/

setcookie('username', 'admin', time()+3600);

```

现在访问这个页面，之后在浏览器设置中可以看到cookie的详细信息：

![](https://i.imgur.com/Sedl8wv.png)

而且可以在network中看到响应头：

    Set-Cookie: username=admin; expires=Mon, 22-Apr-2019 15:50:42 GMT; Max-Age=3600

可以看到返回的cookie的名字和数据，以及失效时间(expires)；到了失效时间，浏览器会自动删除cookie；不同浏览器存储cookie位置是不一样的，即不同浏览器不会共享cookie

第一次请求时在请求头中并不会看到cookie这个字段，但刷新页面，会发现请求头中多出来了：

	Cookie: username=admin

## [获取cookie](#获取cookie)

$_COOKIE:超全局数组，获取传递过来的cookie

cookie.php    
``` php     
<?php

var_dump($_COOKIE);

setcookie('username', 'admin', time()+3600);
```

这样就可以获取到cookie；如果第一次访问是不会显示cookie的，只有服务端发送了cookie给客户端，客户端第二次请求的时候才能显示：

![](https://i.imgur.com/w2EaAXI.png)

那么，在服务端的哪些位置可以使用$_COOKIE获取cookie呢？

setcookie函数一共有七个参数，除了上面三个常用的参数，第四个参数还可以设置那些路径可以接收cookie；在默认的情况下(不设置第四个参数)，同级目录下的文件都可以获取到cookie

比如，现在文件路径为`\WWW\MessageBoard\cookie.php`，那么在默认情况下，`\WWW\MessageBoard\`目录下的所有文件都可以通过$_COOKIE获取cookie

现在想让`\WWW\`下的所有文件获取cookie.php中的cookie，那么只需设置cookie路径为根目录即可：

``` php
<?php

setcookie('username', 'admin', time()+3600, '\')
```

除了用第四个参数设置路径，还可通过设置第五个参数域名设置cookie

setcookie最后两个参数：

- secure
设置成 TRUE 时，只有HTTPS连接存在时才会设置 Cookie。
- httponly
设置成 TRUE，Cookie 仅可通过 HTTP 协议访问。

## [删除cookie](#删除cookie)

可以通过设置cookie过期时间来删除cookie：

``` php
<?php
var_dump($_COOKIE);

setcookie('username', '', time()-1);
```

此时刷新两次页面后，在请求头中便找不到cookie这个字段了；在响应头中发现：

	Set-Cookie: username=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0

## [session](#session)

- $_SESSION:超全局变量数组
- session_start():启动新会话或者重用现有会话
- session_name():session的名称，默认为PHPSESSID；从phpp.ini中获得
- session_id():session的id，随机的32位字符串；没有session_start()就不会有session_id

session.php    
```
<?php

session_start();
/*
首先判断$_COOKIE[session_name()]是否有值，即是否存在session_id；为空则会生成一个session_id，之后将session_id加上前缀`sess_`生成文件，并且通过cookie的方式将session_id传到客户端
如果存在，则会查找这个session_id对应的文件，找到后会反序列化这个文件的内容，将这些内容存到$_SESSION中；如果没找到对应的文件，则会根据这个session_id新建一个session文件
*/

$_SESSION['username'] = 'jack';
/*
给SESSION这个数组里面添加值；脚本结束之后会将名称`username`直接写入session_start()获得的session_id对应的session文件中；数据'jack'根据session.serialize_handler设置的序列化方法存储到session文件中
*/
```

访问这个页面，会发现响应头有这个字段：

	Set-Cookie: PHPSESSID=h0lsdjqmb9juhq1ptp0duucgt6; path=/

此时，在`phpStudy\PHPTutorial\tmp\tmp`这个目录下会发现一个session文件，文件名为：

	sess_h0lsdjqmb9juhq1ptp0duucgt6

以`sess_`开头，里面的内容为：

	username|s:4:"jack";


session文件保存的位置或其他有关session的配置可以在`php.ini`中查看和修改；使用的php版本为5.5.38，则可以在`phpStudy\PHPTutorial\php\php-5.5.38`中找到对应的php.ini

- session.save_handler = files 使用文件的方式来保存session 
- session.save_path 保存路径
- session.use_strict_mode = 0 严格模式不能接收未初始化的session_id
- session.auto_start = 0 是否默认开启session
- session.serialize_handler = php 序列化句柄；设置存储session的序列化方式，默认为php的serialize()

## session销毁

session.php   
``` php
<?php

session_start();

$_SESSION['name'] = 'jack';
```

现在，访问这个页面，会在temp目录下找到对应的session文件，查看属性：

![](https://i.imgur.com/ZZCUSj1.png)

创建时间和修改时间是一样的；再次访问这个页面，可以看到修改时间发生了改变：

![](https://i.imgur.com/TIkVfFV.png)

原因是：再次访问这个页面时，由于通过cookie传过去了session_id，则会反序列化这个session文件的内容，存到$_SESSION中；虽然没有改变键值对数据，但脚本运行完还是会将$_SESSION内容进行序列化存到session文件中，即不管有没有改变，都会覆盖掉以前的数据

php.ini:

- session.gc_maxlifetime = 1440 经过1440s后文件没有改动，被认为此session文件是过期文件
- session.gc_divisor = 1000 session_start()启动1000次，会启动垃圾回收机制，删除过期文件；



# [数据库管理](#数据库管理)

用数据库需要存储用户的：

- 用户名
- 留言时间
- 留言内容

