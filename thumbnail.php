<?
//if(!defined("IN_TRACKER")) die("Direct access to this page not allowed");

# Constants
define("IMAGE_BASE", 'torrents/images');
define("THUMBS_BASE", 'torrents/thumbnail');  //Xlab

$for=(isset($_GET['for']) ? htmlentities($_GET['for']):"");

if (isset($for)) {
	
if ($for == 'block')
define("MAX_WIDTH", 140);

elseif ($for == 'browse')
define("MAX_WIDTH", 100);

elseif ($for == 'details')
define("MAX_WIDTH", 512);

elseif ($for == 'beta')
define("MAX_WIDTH", 85);

elseif ($for == 'rss')
define("MAX_WIDTH", 100);

elseif ($for == 'getdetals')
define("MAX_WIDTH", "400");
}
else {
create_error();
}

// Check DIR

if (!is_dir(THUMBS_BASE)) mkdir(THUMBS_BASE);

# Get image location
$image_file = trim(htmlentities($_GET['image']));
$image_path = IMAGE_BASE . "/".$image_file;
    //File extention
    
//$ext = substr($image_file, strlen($image_file)-3, 3);
$ext = end(explode('.',$image_file));

$id = substr($image_file, 0, strlen($image_file)-4);

$thumb_path = THUMBS_BASE."/".$id.$for.".".$ext;

if (file_exists($thumb_path)) {
  header("Location: ".$thumb_path);
  die();
}


# Load image
$img = null;
$ext = strtolower(end(explode('.', $image_path)));


if ($ext == 'jpg' || $ext == 'jpeg') {
$img = @imagecreatefromjpeg($image_path);
} elseif ($ext == 'png') {
$img = @imagecreatefrompng($image_path);
# Only if your version of GD includes GIF support
} elseif ($ext == 'gif') {
$img = @imagecreatefromgif($image_path);
}

# If an image was successfully loaded, test the image for size
if ($img) {

# Get image size and scale ratio
$width = imagesx($img);
$height = imagesy($img);
$scale = MAX_WIDTH/$width;

# If the image is larger than the max shrink it
if ($scale < 1) {
if ($width > MAX_WIDTH) {
$new_width = floor($scale*$width);
$new_height = floor($scale*$height);
# Create a new temporary image
$tmp_img = imagecreatetruecolor($new_width, $new_height);
# Copy and resize old image into new image
imagecopyresampled($tmp_img, $img, 0, 0, 0, 0,
$new_width, $new_height, $width, $height);
imagedestroy($img);
//imagedestroy($tmp_img);
$img = $tmp_img;
}
}else{
header("Location: ".$image_path);
die();
}} else create_error();

# Create error image if necessary
function create_error() {

$image_file = trim(htmlentities($_GET['image']));

if (!file_exists("torrents/images/".$image_file)) {

$img = imagecreate(100,100);
imagecolorallocate($img,0,0,0);
$c = imagecolorallocate($img,255,255,255);
        $str = "No image ";
        $str2 = $image_file;
 // определяем координаты вывода текста
        $size = 2; // размер шрифта
        $x_text = imagefontwidth($size)*strlen($str)-3;
        $y_text = imagefontheight($size)-3;

       imagestring($img, $size, 3, 3, $str,$c);
       imagestring($img, $size, 3, 10, $str2,$c);
header("Content-type: image/jpeg");
imagejpeg($img);

} else {
header("Location: torrents/images/".$image_file);
}

die();
}

//--------------------------------------------------Xlab---------------
function save_image($image, $file, $extention){

		if (file_exists($file)){
			@unlink($file);
			save_image($image, $file, $extention);	
} else {

		switch ($extention){

			case "jpg" :
				imagejpeg($image, $file);
				header("Content-type: image/jpeg");
				imagejpeg($image);
				break;
				
			case "jpeg" :
				imagejpeg($image, $file);
				header("Content-type: image/jpeg");
				imagejpeg($image);
				break;	

			case "png" :
				imagepng($image, $file);
				header("Content-type: image/png");
				imagepng($image);
				break;

			case "gif" :
				imagegif($image, $file);
				header("Content-type: image/gif");
				imagegif($image);
				break;

		}

		return true;

	}
}

save_image($img,$thumb_path,$ext);


?>