<?php
use Imagine\Image\ImageInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\Point;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;

function timestamp($image, $imagine)
{
  $font    = $imagine->font(null, 36, (new RGB())->color('0000ff'));
  $canvas->draw()->text(time(), null, new Point(0, 0), 135);
  return $image;
}

function fit($file, $imagine, $dw, $dh)
{
  $image     = $imagine->open($file->getRealPath());
  $srcBox = $image->getSize();
  $sw = $srcBox->getWidth();
  $sh = $srcBox->getHeight();
  
  $x_ratio = $dw / $sw;
  $y_ratio = $dh / $sh;
 
 
  $golden_ratio = min($x_ratio, $y_ratio);
  $width = floor($sw * $golden_ratio);
  $height = floor($sh * $golden_ratio);
  
  //   var_dump([
  //     "X ratio: {$x_ratio}",
  //     "Y ratio: {$y_ratio}",
  //     "Golden ratio: {$golden_ratio}",
  //     "Requested size: {$dw}x{$dh}",
  //     "Source size: {$sw}x{$sh}",
  //     "Width difference: " .($dw-$sw),
  //     "Height difference: " . ($dh-$sh),
  //     "Resize size: {$width}x{$height}",
  // ]);
  $image->resize(new Box($width, $height));
  
  return $image;
}

function letterbox($image, $imagine, $w, $h)
{
  $background = $imagine->create(new Box($w,$h));
  
  
  $centered = new Imagine\Image\Point(
    ($w - $image->getSize()->getWidth())/2, 
    ($h - $image->getSize()->getHeight())/2
  );

  $background->paste($image, $centered);

  return $background;
}

function frameit($file, $imagine, $dw, $dh)
{
  $image = fit($file, $imagine, $dw, $dh);
  $image = letterbox($image, $imagine, $dw, $dh);
  return $image;
}

/* Specify an array of sizes to be maintained */
$sizes = [
  'facebook'=>function($file, $imagine) {
    $image = fit($file, $imagine, 630, 630);
    $image = letterbox($image, $imagine, 1200, 630);
    return $image;
  },
  'thumb' => function($file, $imagine) {
    return frameit($file, $imagine, 180,180);
  },
  'admin' => function($file, $imagine) {
    return frameit($file, $imagine, 100,100);
  },
  'tiny' => function($file, $imagine) {
    $image = frameit($file, $imagine, 75,75);
    return $image;
  },
];
  
foreach($sizes as $k=>$v)
{
  $sizes[$k] = [
    'dimensions'=>$v,
    'convert_options' => [
      'jpeg_quality' => 90,
      'resampling-filter' => ImageInterface::FILTER_CATROM,
    ],
  ];
}

return [
  'sizes'=>$sizes,
];
