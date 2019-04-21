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
	- [php实现](#php实现)
	- [数据库管理](#数据库管理)
- 会话管理
	- [cookie、session模式](#cookie、session模式)

环境：

- windows10
- phpstudy2018，PHP-5.5.38

# php操作mysql
## 选择pdo操作数据库

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

## 使用pdo连接数据库

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

## 操作数据库
使用创建好的pdo对象来实现对数据表的增、删、改、查;

实现很简单，只需调用pdo对象的exec方法，执行相应的mysql语句即可

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


$sql = 'drop table hello';


/*添加数据
$sql = "insert into hello(id, email, age) values (1,'123456789@qq.com', 18)";

*/

/*修改数据
$sql = "update hello set email='123@qq.com', age=20 where id=1";
*/

/*删除数据
$sql = "delete from hello where id=1";
*/

$res = $pdo->exec($sql);
var_dump($res);
?>
```