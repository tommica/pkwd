<?php
// powered by mimvic - https://code.google.com/p/mimvic/
require('uvic.php');
use MiMViC as mvc;

//We only want to serve two different pages, frontpage and the image
mvc\get('/:width/:height', 
  function ($params){
    //Placekitten only outputs jpg images, so we set the right headers
    header('Content-type: image/jpeg');
  
    //Setting/Getting some variables
    $width = abs((int)strip_tags($params['width']));
    $height = abs((int)strip_tags($params['height']));
    $url = 'http://placekitten.com/';
    $get_url = $url.$width.'/'.$height;

    //Get the image
    $jpg_image = imagecreatefromjpeg($get_url);

    //Set textcolor and stroke color
    $gray = imagecolorallocate($jpg_image, 45, 45, 45);
    $white = imagecolorallocate($jpg_image, 255, 255, 255);

    //Define fonts and sizes - We're scaling the fontsize so it can fits the image... Could be done better, this is pretty crude way of doing it 
    $font_path = './BLUEPSS_.TTF';
    $font_size = $width < $height ? ($width/10) : ($height/10);

    // You don't have to set a max size for the font, I just thought it would be nice
    $font_size = $font_size > 120 ? 120 : $font_size;
    $stroke_size = 2;

    //Setting the text
    $text = $width.' / '.$height;

    //Get the bounding box for the text
    $box = imagettfbbox($font_size, 0, $font_path, $text);

    //And the center position
    $x = $box[0] + ($width / 2) - ($box[4] / 2);
    $y = $box[1] + ($height / 2) - ($box[5] / 2);

    //Got this function from http://pastebin.com/qmxuZSbq - makes my life easier
    imagettfstroketext($jpg_image, $font_size, 0, $x, $y, $gray, $white, $font_path, $text, $stroke_size);

    //Serve the image, and clear memory
    imagejpeg($jpg_image);
    imagedestroy($jpg_image);
  }
);

// This is for redirecting everything else to frontpage, and serving the frontpage
mvc\get('/*',
  function($params){
    if(empty($params['segments'][0][0])) {
      echo file_get_contents('front.html');
    } else {
      header('location: .');
    }
  }
);

mvc\start();

// http://pastebin.com/qmxuZSbq
function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
  for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
  for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
  $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
  
  return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
} 
?>
