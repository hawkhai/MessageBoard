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
