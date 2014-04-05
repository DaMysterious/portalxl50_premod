<?php
/**
*
* @package 4seven / First Topic [pic] on Forum Index  / 2010	
* @version $Id: thumb_crop.php 2356 2010-03-31 00:02:12Z 4seven $
* @copyright (c) 2009 4seven
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (function_exists('exif_imagetype')){

// echo 'exif_crop';

class cropimage {

var $source_image;
var $new_image_name;
var $save_to_folder;

function crop($location = 'center')
{
$info   = getimagesize($this->source_image);
$width  = $info[0];
$height = $info[1];
$mime   = exif_imagetype($this->source_image);

if($width == $height)
{
// do nothing 
}
else
{

switch ($mime)
{
case 2:
    $image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
    break;

case 3:
    $image_create_func = 'ImageCreateFromPNG';
    $image_save_func = 'ImagePNG';
	$new_image_ext = 'png';
    break;

case 1:
    $image_create_func = 'ImageCreateFromGIF';
    $image_save_func = 'ImageGIF';
	$new_image_ext = 'gif';
    break;

default:
	$image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
}

   if($width > $height)
   {
	   if($location == 'center')
       {
       $x_pos = ($width - $height) / 2;
       $x_pos = ceil($x_pos);

       $y_pos = 0;
	   }
	   else if($location == 'left')
	   {
	   $x_pos = 0;
	   $y_pos = 0;
	   }
	   else if($location == 'right')
	   {
	   $x_pos = ($width - $height);
	   $y_pos = 0;
	   }

       $new_width = $height;
       $new_height = $height;
   }
   else if($height > $width)
   {
	   if($location == 'center')
       {
       $x_pos = 0;

       $y_pos = ($height - $width) / 2;
       $y_pos = ceil($y_pos);
       }
	   else if($location == 'left')
	   {
	   $x_pos = 0;
	   $y_pos = 0;
	   }
	   else if($location == 'right')
	   {
	   $x_pos = 0;
	   $y_pos = ($height - $width);
	   }

       $new_width = $width;
       $new_height = $width;

   }

$image = $image_create_func($this->source_image);

$new_image = imagecreatetruecolor($new_width, $new_height);

imagecopy($new_image, $image, 0, 0, $x_pos, $y_pos, $width, $height);

if($this->save_to_folder)
		{
	       if($this->new_image_name)
	       {
	       $new_name = $this->new_image_name.'.'.$new_image_ext;
	       }
	       else
	       {
	       $new_name = $this->new_image_name( basename($this->source_image) ).'_square_'.$location.'.'.$new_image_ext;
	       }

		$save_path = $this->save_to_folder.$new_name;
		}

$process = $image_save_func($new_image, $save_path) or die("There was a problem in saving the new file.");

return array('result' => $process, 'new_file_path' => $save_path);

}
}

function new_image_name($filename)
	{
	
	$string = $filename;

	return $string;
	}
}

}

else{

class cropimage {

var $source_image;
var $new_image_name;
var $save_to_folder;

function crop($location = 'center')
{
$info = getimagesize($this->source_image);

$width = $info[0];
$height = $info[1];
$mime = $info['mime'];

if($width == $height)
{
// do nothing 
}
else
{

$type = substr(strrchr($mime, '/'), 1);

switch ($type)
{
case 'jpeg':
    $image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
    break;

case 'png':
    $image_create_func = 'ImageCreateFromPNG';
    $image_save_func = 'ImagePNG';
	$new_image_ext = 'png';
    break;

case 'gif':
    $image_create_func = 'ImageCreateFromGIF';
    $image_save_func = 'ImageGIF';
	$new_image_ext = 'gif';
    break;
	
default:
	$image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
}

   if($width > $height)
   {
	   if($location == 'center')
       {
       $x_pos = ($width - $height) / 2;
       $x_pos = ceil($x_pos);

       $y_pos = 0;
	   }
	   else if($location == 'left')
	   {
	   $x_pos = 0;
	   $y_pos = 0;
	   }
	   else if($location == 'right')
	   {
	   $x_pos = ($width - $height);
	   $y_pos = 0;
	   }

       $new_width = $height;
       $new_height = $height;
   }
   else if($height > $width)
   {
	   if($location == 'center')
       {
       $x_pos = 0;

       $y_pos = ($height - $width) / 2;
       $y_pos = ceil($y_pos);
       }
	   else if($location == 'left')
	   {
	   $x_pos = 0;
	   $y_pos = 0;
	   }
	   else if($location == 'right')
	   {
	   $x_pos = 0;
	   $y_pos = ($height - $width);
	   }

       $new_width = $width;
       $new_height = $width;

   }

$image = $image_create_func($this->source_image);

$new_image = imagecreatetruecolor($new_width, $new_height);

imagecopy($new_image, $image, 0, 0, $x_pos, $y_pos, $width, $height);

if($this->save_to_folder)
		{
	       if($this->new_image_name)
	       {
	       $new_name = $this->new_image_name.'.'.$new_image_ext;
	       }
	       else
	       {
	       $new_name = $this->new_image_name( basename($this->source_image) ).'_square_'.$location.'.'.$new_image_ext;
	       }

		$save_path = $this->save_to_folder.$new_name;
		}

$process = $image_save_func($new_image, $save_path) or die("There was a problem in saving the new file.");

return array('result' => $process, 'new_file_path' => $save_path);

}
}

function new_image_name($filename)
	{
	
	$string = $filename;
	return $string;
	}
}

}

?>