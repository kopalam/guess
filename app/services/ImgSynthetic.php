<?php
	
	Class  ImgSynthetic{
		/*

			合成图片类

		*/
			function  SyntheticImg($imgUrl,$issued_id,$contents)
			{
				 header("content-type: image/png");//如果要看报什么错，可以先注释调这个header  
				    // $nickname = "昵称";//微信昵称  
				    // $erweimaurl = "image/erweima.png";//二维码  
				    // $logourl = "image/0.png";//微信头像   
				    // $beijing = "image/1.png";//海报最底层得背景  
				    $beijing = imagecreatefrompng($imgUrl);  
				    // $logourl = imagecreatefrompng($logourl);  
				    // $erweimaurl = imagecreatefrompng($erweimaurl);  
				    $image_3 = imageCreatetruecolor(imagesx($beijing),imagesy($beijing));  
				    $color = imagecolorallocate($image_3, 255, 255, 255);  
				    imagefill($image_3, 0, 0, $color);  
				    imageColorTransparent($image_3, $color);  
				    imagecopyresampled($image_3,$beijing,0,0,0,0,imagesx($beijing),imagesy($beijing),imagesx($beijing),imagesy($beijing));     
				    //字体颜色  
				    $white = imagecolorallocate($image_3, 111, 255, 255);  
				    $rqys = imagecolorallocate($image_3, 255, 255, 255);  
				    $black = imagecolorallocate($image_3,120,84,26);  
				    $font = "simhei.ttf";  //写的文字用到的字体。字体最好用系统有得，否则会包charmap的错，这是黑体   
				    //imagettftext设置生成图片的文本      
				    imagettftext($image_3,32,0,240,55,$rqys,$font,$contents);  
				    imagecopymerge($image_3,$logourl, 0,0,0,0,160,160,100);//左，上，右，下，宽度，高度，透明度  
				    //imagecopymerge($image_3,$erweimaurl, 120,100,0,0,imagesx($erweimaurl),imagesy($erweimaurl), 100);  
				    // imagecopymerge($image_3,$erweimaurl, 120,100,0,0,imagesx($erweimaurl),imagesy($erweimaurl), 100);  
				    //生成图片  
				    // return $image_3;
				    return imagepng($image_3);//在浏览器上显示  
				    //imagepng($image_3,"d:\a.png");保存到本地  
				    imagedestroy($im);  
			}
	}