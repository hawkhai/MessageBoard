<?php

class Mysql
{

	public $username; 
	public $password; //接收到的用户名和密码(注册和登录时使用)
	public $content; //接收留言内容

	public $pdo; //pdo对象
	public $db_username = 'root';
	public $db_password = '123456';
	public $sql; //sql语句
	public $table; //数据表

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


	public function Exec()
	{
		$this->res = $this->pdo->exec($this->sql);
	}

	public function Query()
	{
		
		$this->res = $this->pdo->query($this->sql);

	}

	public function Create()
	{
		$this->sql = 'create table $this->table(
			    id int unsigned not null auto_increment,
			    username varchar(20) not null,
			    password varchar(20) not null,
			    primary key(id)
		    	);';
	}

	public function Select()
	{
		$this->sql = "select * from $this->table";
		$this->Query();
	}
	

	public function Drop()
	{
		$this->sql = "drop table $this->table";
		$this->Exec();
	}

	public function Insert() //注册
	{
		$this->sql = "insert into $this->table(username, password) values ('" . "$this->username'" . ",'" . "$this->password')";

		$this->Exec();
	}
	

	public function Leave_mess() //留言
	{
		$this->sql = "insert into $this->table(users_id , username, content, time) values ((select id from users where username='" . "$this->username')," . "'$this->username" . "'," . "'$this->content" . "', now())";

		$this->Exec();
	}
	
	public function Delete()
	{
		$this->sql = "delete from $this->table where id=1";
	}

	public function Login()
	{
		$this->sql = "select * from $this->table where username='" . "$this->username'";
		
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