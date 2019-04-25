<?php

class Mysql
{

	public $username; 
	public $password; //接收到的用户名和密码

	public $pdo; //pdo对象
	public $db_username = 'root';
	public $db_password = '123456';
	public $sql; //sql语句

	public $res; //执行查询语句返回的对象

	//数据源名称，包含了请求连接到数据库的信息
	public $dsn = 'mysql:dbname=MessageBoard;host=127.0.0.1';
	function __construct()
	{
		try
		{
			$this->pdo = new PDO($this->dsn, $this->db_username, $this->db_password);
		} 
		catch(PEOException $e)
		{
			echo '数据库连接失败！';
		}
	}


	public function Exec($sql)
	{
		$res = $this->pdo->exec($sql);
	}

	public function Create()
	{
		$sql = 'create table user(
			    id int unsigned not null auto_increment,
			    username varchar(20) not null,
			    password varchar(20) not null,
			    primary key(id)
		    	);';
	}

	public function Select()
	{
		$sql = 'select * from user';
	}
	

	public function Drop()
	{
		$sql = 'drop table user';
	}

	public function Insert()
	{
		$this->sql = "insert into user(username, password) values ('" . "$this->username'" . ",'" . "$this->password')";

		$this->Exec($this->sql);
	}
	

	public function Update()
	{
		$sql = "update user set email='123@qq.com', age=20 where id=1";
	}
	
	public function Delete()
	{
		$sql = "delete from user where id=1";
	}

	public function Query()
	{
		$this->sql = "select * from user where username='" . "$this->username'";
		
		$this->res = $this->pdo->query($this->sql);

		foreach ($this->res as $row) {
			if($row['password'] === $this->password){
				return true;			}
			else{
				return false;
			}
		}
	}
	
}
?>