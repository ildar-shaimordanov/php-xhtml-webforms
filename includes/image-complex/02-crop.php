<?php

$filename = 'includes/image-images/image.jpg';

if ( @$_GET['show_image'] == 'yes' ) {

#	require_once '../../Core.php';
	require_once 'Image.php';

	$image =& new Image($filename);

	// size of an image
	$width  = $image->width();
	$height = $image->height();

	// left-top corner of the cropped part
	$left   = $width / 4;
	$top    = $height / 4;

	// width and height of the cropped part
	$width  = $width / 2;
	$height = $height / 2;

	$image->crop($width, $height, $left, $top);

	$image->output();

	exit;

}

$title = 'Crop of image';

?>

<table border="1">
<tr align="center" valign="top">
<td>
<h3>Original</h3>
<img src="<?=$filename?>" />
</td>
<td>
<h3>Middle part</h3>
<img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=crop&show_image=yes" />
</td>
</tr>
</table>

