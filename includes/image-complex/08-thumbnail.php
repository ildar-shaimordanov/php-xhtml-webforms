<?php

$filename = 'includes/image-images/image.jpg';

$width   = 150;
$height  = 150;
$percent = 0.5;


if ( @$_GET['show_image'] == 'yes' ) {

#    require_once '../../Core.php';
    include_once 'Image/Thumbnail.php';

    if ( ! isset($_GET['method']) || $_GET['method'] < Image::METHOD_SCALE_MAX || $_GET['method'] > Image::METHOD_CROP ) {
        $_GET['method'] = Image::METHOD_SCALE_MAX;
    }

    // Creating of thumbnail
    $image =& new Image_Thumbnail($filename);
    $image->output(null, array(
        'width'   => $width,
        'height'  => $height,
        'method'  => $_GET['method'],
        'percent' => $percent,
    ));

    exit;

}

$title = 'Thumbnails within ' . $width . ' x ' . $height;

?>

<table border="1" class="bodyTable">
<tr align="center" valign="top">
<td>
<h3>Original Image</h3>
<img src="<?=$filename?>" />
</td>
<td width="150">
<h5>Maximal scaling</h5>
<div class="thumbFrame"><img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=thumbnail&show_image=yes&method=0" /></div>
</td>
<td width="150">
<h5>Minimal scaling</h5>
<div class="thumbFrame"><img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=thumbnail&show_image=yes&method=1" /></div>
</td>
<td width="150">
<h5>Crop with <?=$percent > 1 ? $percent : $percent * 100?>%</h5>
<div class="thumbFrame"><img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=thumbnail&show_image=yes&method=2" /></div>
</td>
</tr>
</table>

