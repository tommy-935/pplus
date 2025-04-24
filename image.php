<?php
/*
$im = imagecreatetruecolor(200, 200);
$red = imagecolorallocate($im, 255, 255, 255);
imagerectangle($im, 350, 20, 500, 170, $red);
imagefilledrectangle($im, 0, 0, 500, 170, $red);
header("content-type: image/png");
// imagepng($im, 'a.png'); // save
// imagepng($im, 'a.png');
imagejpeg($im, 'a.jpg');
imagedestroy($im);
die;
*/

// sanjiao();
function sanjiao(){
    //create a white canvas
    $imt = @imagecreate(40, 40) or die("Cannot Initialize new GD image stream");
    imagecolorallocate($imt, 255, 255, 255);
    //triangle
    /*
    $t1 = rand(0,40);
    $t2 = rand(0,40);
    $t3 = rand(0,10);
    $t4 = rand(0,10);
    $points = array(
    $t1, $t2,
    ($t1+$t3), $t2,
    $t1, ($t2+$t4)
    );
    */
    $points = [
        0, 0,
        40, 40,
        0, 40
    ];
    // $trcol = imagecolorallocate($im, rand(0,255), rand(0,255), rand(0,255));
    $trcol = imagecolorallocatealpha($imt, 255, 255, 255, 127);
    imagefilledpolygon($imt, $points, 3, $trcol);
    //make png and clean up
    header("Content-type: image/png");
    imagepng($imt);
    imagedestroy($imt);
}

// caijian();
function caijian(){
    $img = 'b.jpg';
    $width = 200;
    $height = 200;
    $source = imagecreatefromjpeg($img);
    $crop = imagecreatetruecolor($width, $height);
    imagecopy($crop, $source, 0, 0, 5, 10, 20, 40);

    imagesavealpha($crop, true);
    //拾取一个完全透明的颜色,最后一个参数127为全透明
    $bg = imagecolorallocatealpha($crop, 255, 255, 85, 27);
    imagefill($crop, 0, 0, $bg);
    $r   = $w / 2; //圆半径
    $y_x = $r; //圆心X坐标
    $y_y = $r; //圆心Y坐标
    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            $rgbColor = imagecolorat($img, $x, $y);
            if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                imagesetpixel($crop, $x, $y, $rgbColor);

            }

        }

    }
    header('Content-Type: image/jpeg');
    imagejpeg($crop);
    imagedestroy($crop);
    imagedestroy($source);
}

// shadow();
function shadow(){
    /* set drop shadow options */

/* offset of drop shadow from top left */
define("DS_OFFSET",  20);
 
/* number of steps from black to background color */
define("DS_STEPS", 10);

/* distance between steps */
define("DS_SPREAD", 1);

/* define the background color */
$background = array("r" => 255, "g" => 255, "b" => 255);

$src = 'aa.jpg';
if(isset($src) && file_exists($src)) {

  /* create a new canvas.  New canvas dimensions should be larger than the original's */
  list($o_width, $o_height) = getimagesize($src);
  $width  = $o_width + DS_OFFSET;
  $height = $o_height + DS_OFFSET;
  $image = imagecreatetruecolor($width, $height);

  /* determine the offset between colors */
  $step_offset = array("r" => ($background["r"] / DS_STEPS), "g" => ($background["g"] / DS_STEPS), "b" => ($background["b"] / DS_STEPS));

  /* calculate and allocate the needed colors */
  $current_color = $background;
  for ($i = 0; $i <= DS_STEPS; $i++) {
    $colors[$i] = imagecolorallocate($image, round($current_color["r"]), round($current_color["g"]), round($current_color["b"]));

    $current_color["r"] -= $step_offset["r"];
    $current_color["g"] -= $step_offset["g"];
    $current_color["b"] -= $step_offset["b"];
  }

  /* floodfill the canvas with the background color */
  imagefilledrectangle($image, 0,0, $width, $height, $colors[0]);

  /* draw overlapping rectangles to create a drop shadow effect */
  for ($i = 0; $i < count($colors); $i++) {
    imagefilledrectangle($image, DS_OFFSET, DS_OFFSET, $width, $height, $colors[$i]);
    $width -= DS_SPREAD;
    $height -= DS_SPREAD;
  }

  /* overlay the original image on top of the drop shadow */
  $original_image = imagecreatefromjpeg($src);

  imagecopymerge($image, $original_image, 0,0, 0,0, $o_width, $o_height, 100);

  /* output the image */
  header("Content-type: image/jpeg");
  imagejpeg($image);
  
  /* clean up the image resources */
  imagedestroy($image);
  imagedestroy($original_image);
}
}

// composeImg('a.jpg', 'b.jpg', 0, 0);

function composeImg($bigImgPath,$qCodePath, $dst_x,$dst_y){

   // $bigImg = imagecreatefromstring(file_get_contents($bigImgPath));
    $bigImg = imagecreatetruecolor(200, 200);
    imagecolorallocatealpha($bigImg, 255, 255, 255, 127);
    $qCodeImg = imagecreatefromstring(file_get_contents($qCodePath));
    imagesavealpha($bigImg,true);//假如是透明PNG图片，这里很重要 意思是不要丢了图像的透明<code class="php spaces hljs"></code>
    list($qCodeWidth, $qCodeHight, $qCodeType) = getimagesize($qCodePath);
    list($aimg_width, $aimg_height, $aimg_type) = getimagesize($bigImgPath);
    $h = 40;
    $diff_x = $aimg_width - $h;

    $image_p = imagecreatetruecolor($aimg_width, $qCodeWidth);
    // 拉伸图片
    imagecopyresampled($image_p, $qCodeImg,0,0,0,0, $aimg_width, $h, $qCodeWidth,$qCodeHight);

    $b_top = imagecreatetruecolor(200, 40);
    //这一句一定要有
    imagesavealpha($b_top, true);
    //拾取一个完全透明的颜色,最后一个参数127为全透明
    $bg = imagecolorallocatealpha($b_top, 255, 255, 255, 127);
    imagefill($b_top, 0, 0, $bg);
    for ($x = 0; $x <= $aimg_width; $x++) {
        for ($y = 0; $y <= $h; $y++) {
            $rgbColor = imagecolorat($image_p, $x, $y);
            if ($x >= $y && $x <= $diff_x) {
                imagesetpixel($b_top, $x, $y, $rgbColor);
            }else if($x > $diff_x && (($x - $diff_x) + $y) < ($h + 2) ){
                imagesetpixel($b_top, $x, $y, $rgbColor);
            }
        }
    }


    // 三角形填充
    /*
    $imt = @imagecreate(40, 40);
    // imagecolorallocate($imt, 255, 255, 255);
    imagecolorallocatealpha($imt, 255, 255, 255, 127);
    $points = [
        0, 0,
        40, 40,
        0, 40
    ];
    $trcol = imagecolorallocatealpha($imt, 155, 5, 215, 0);
    imagefilledpolygon($imt, $points, 3, $trcol);
    imagecopymerge($image_p, $imt, $dst_x, $dst_y, 0, 0, 40, $h, 100);
    */

    // imagecopymerge使用注解 合拼图片
    // imagecopymerge($bigImg, $image_p, $dst_x, $dst_y, 0, 0, $aimg_width, $h, 100);
    // imagecopymerge($bigImg, $imgg, $dst_x, $dst_y, 0, 0, $aimg_width, $h, 100);
    imagecopy($bigImg, $b_top, $dst_x, $dst_y, 0, 0, $aimg_width, $h);

    //创建一个新的图片资源，用来保存沿Y轴翻转后的图片
    $bottom = imagecreatetruecolor($aimg_width, $h);
    //沿y轴翻转就是将原图从右向左按一个像素宽度向新资源中逐个复制
    for ($y = 0; $y < $h; $y++) {
        //逐条复制图片本身高度，1个像素宽度的图片到薪资源中
        imagecopy($bottom, $b_top, 0, $h - $y - 1, 0, $y, $aimg_width, 1);
    }
   // $image_buttom = imagecreatetruecolor($aimg_width, $qCodeWidth);
    // 拉伸图片
   // imagecopyresampled($image_buttom, $new,0,0,0,0, $aimg_width, $h, $qCodeWidth,$qCodeHight);
    imagecopy($bigImg, $bottom, $dst_x, $aimg_height - $h, 0, 0, $aimg_width, $h);

    // 旋转
    $b_left = imagerotate($b_top, 90, 0);
    /*
    $imgg2 = imagecreatetruecolor($h, $aimg_width);
    //这一句一定要有
    imagesavealpha($imgg2, true);
    //拾取一个完全透明的颜色,最后一个参数127为全透明
    $bg = imagecolorallocatealpha($imgg2, 255, 255, 255, 127);
    imagefill($imgg2, 0, 0, $bg);
    for ($x = 0; $x <= $h; $x++) {
        for ($y = 0; $y <= $aimg_width; $y++) {
            $rgbColor = imagecolorat($im_90, $x, $y);
            if ($x <= $y) {
                imagesetpixel($imgg2, $x, $y, $rgbColor);
            }
        }
    }
    */

    // imagecopymerge($bigImg, $im_90, $dst_x, $dst_y, 0, 0, $h, $aimg_height, 100);
    // imagecopymerge($bigImg, $imgg2, $dst_x, $dst_y, 0, 0, $h, $aimg_height, 100);
    // imagecopymerge会失去本身透明
    imagecopy($bigImg, $b_left, $dst_x, $dst_y, 0, 0, $h, $aimg_height);

    
    // 旋转2
    $b_right = imagerotate($b_top, 270, 0);
    imagecopy($bigImg, $b_right, $aimg_width - $h, $dst_y, 0, 0, $h, $aimg_height);

/*
    $new2 = imagecreatetruecolor($qCodeWidth, $qCodeHight);
    //沿y轴翻转就是将原图从右向左按一个像素宽度向新资源中逐个复制
    for ($y = 0; $y < $qCodeHight; $y++) {
        //逐条复制图片本身高度，1个像素宽度的图片到薪资源中
        imagecopy($new2, $im_90, 0, $qCodeHight - $y - 1, 0, $y, $qCodeWidth, 1);
    }
    imagecopy($bigImg, $new2, 60, $dst_y, 0, 0, $h, $aimg_height, 100);
    */

    list($bigWidth, $bigHight, $bigType) = getimagesize($bigImgPath);
    $pa = 'aaa.jpg';
    switch ($bigType) {
        case 1: //gif
            imagegif($bigImg, $pa);
            break;
        case 2: //jpg
            imagejpeg($bigImg, $pa);
            break;
        case 3: //jpg
            imagepng($bigImg, $pa);
            break;
        default:
            imagejpeg($bigImg, $pa);
            break;
    }
    imagedestroy($bigImg);
    imagedestroy($qCodeImg);
}

function trun_x($filename)
{
    $back = imagecreatefromjpeg($filename);
    $width = imagesx($back);
    $height = imagesy($back);

    //创建一个新的图片资源，用来保存沿Y轴翻转后的图片
    $new = imagecreatetruecolor($width, $height);
    //沿y轴翻转就是将原图从右向左按一个像素宽度向新资源中逐个复制
    for ($y = 0; $y < $height; $y++) {
        //逐条复制图片本身高度，1个像素宽度的图片到薪资源中
        imagecopy($new, $back, 0, $height - $y - 1, 0, $y, $width, 1);
    }

    //设置保存的路径
    $imgSrc = 'uploads/' . strtotime(date('YmdHis')) . '.jpg';
    imagejpeg($new, $imgSrc);
    imagedestroy($back);
    imagedestroy($new);
}

// trun_x("images/20201116140315.jpg");
